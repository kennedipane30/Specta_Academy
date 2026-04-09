<?php
namespace App\Http\Controllers;

use App\Models\DedicatedTutor;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;

class DedicatedTutorController extends Controller {

    public function getFormData(Request $request) {
        $user = $request->user()->load('student');
        $materials = Material::where('class_id', $user->student->grade)->get();
        $teachers = User::where('role_id', 2)->get(['usersID', 'name']);

        // AMBIL HISTORI
        $history = DedicatedTutor::with(['teacher', 'material'])
                    ->where('student_id', $user->student->studentsID)
                    ->latest()->get();

        // HITUNG KUOTA (Hanya hitung yang Pending & Confirmed)
        $used_quota = DedicatedTutor::where('student_id', $user->student->studentsID)
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count();

        return response()->json([
            'materials' => $materials,
            'teachers' => $teachers,
            'history' => $history,
            'used_quota' => $used_quota, // Kirim jumlah yang sudah terpakai
            'max_quota' => 3
        ]);
    }

    public function store(Request $request) {
        $student = $request->user()->student;

        // CEK KUOTA SEBELUM SIMPAN
        $count = DedicatedTutor::where('student_id', $student->studentsID)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->count();

        if ($count >= 3) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jatah tutor Anda sudah habis (Maksimal 3 sesi per kelas).'
            ], 422);
        }

        DedicatedTutor::create([
            'student_id' => $student->studentsID,
            'teacher_id' => $request->teacher_id,
            'material_id' => $request->material_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Berhasil mengajukan tutor']);
    }
}
