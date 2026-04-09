<?php

namespace App\Http\Controllers;

use App\Models\DedicatedTutor;
use App\Models\Material;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class DedicatedTutorController extends Controller {

    public function getFormData(Request $request) {
        try {
            // Gunakan ID User agar lebih akurat mencari Enrollment
            $userID = $request->user()->usersID;
            $user = $request->user()->load('student');

            // 1. Perbaikan: Cari pendaftaran berdasarkan user_id (sesuai Model Enrollment kamu)
            $enrollment = Enrollment::where('user_id', $userID)
                            ->where('status', 'confirmed')
                            ->first();

            // Jika tidak ada pendaftaran confirmed, ambil materi secara global agar form tidak kosong
            if ($enrollment) {
                $materials = Material::where('class_id', $enrollment->class_id)->get();
            } else {
                $materials = Material::take(10)->get(); // Backup data materi
            }

            $teachers = User::where('role_id', 2)->get(['usersID', 'name']);

            // Ambil ID Student dengan aman
            $studentID = $user->student->studentsID ?? 0;

            return response()->json([
                'status' => 'success',
                'user_data' => [
                    'name' => $user->name ?? 'User',
                    'nisn' => $user->student->nisn ?? '-',
                ],
                'materials' => $materials,
                'teachers' => $teachers,
                'used_quota' => DedicatedTutor::where('student_id', $studentID)
                                ->whereIn('status', ['pending', 'confirmed'])->count(),
                'max_quota' => 3,
                'history' => DedicatedTutor::with(['teacher', 'material'])
                                ->where('student_id', $studentID)->latest()->get()
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request) {
        try {
            $student = $request->user()->student;
            if (!$student) return response()->json(['status' => 'error', 'message' => 'Profil belum lengkap'], 422);

            $count = DedicatedTutor::where('student_id', $student->studentsID)
                        ->whereIn('status', ['pending', 'confirmed'])->count();

            if ($count >= 3) return response()->json(['status' => 'error', 'message' => 'Kuota habis'], 422);

            DedicatedTutor::create([
                'student_id' => $student->studentsID,
                'teacher_id' => $request->teacher_id,
                'material_id' => $request->material_id,
                'date' => $request->date,
                'time' => $request->time,
                'status' => 'pending'
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
