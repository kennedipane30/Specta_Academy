<?php

namespace App\Http\Controllers\Api\pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\DedicatedTutor;
use App\Models\Material;

class DedicatedTutorController extends Controller
{
    public function getTutorFormData()
    {
        try {
            $user = Auth::user();
            // Cari data student berdasarkan usersID (Primary Key Anda)
            $student = Student::where('user_id', $user->usersID)->first();

            if (!$student || is_null($student->class_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Akses terbatas. Anda belum masuk kelas.',
                    'materials' => []
                ], 200);
            }

            // Ambil materi sesuai class_id siswa
            $materials = Material::where('class_id', $student->class_id)
                        ->get(['materialsID', 'title']);

            return response()->json([
                'status' => 'success',
                'materials' => $materials
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->usersID)->first();
            if (!$student) return response()->json(['data' => []]);

            $history = DedicatedTutor::with(['material', 'teacher'])
                        ->where('student_id', $student->studentsID)
                        ->latest()
                        ->get();

            return response()->json(['status' => 'success', 'data' => $history]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $student = Student::where('user_id', $user->usersID)->first();

            $dedicated = DedicatedTutor::create([
                'student_id'  => $student->studentsID,
                'teacher_id'  => null,
                'material_id' => $request->material_id,
                'date'        => $request->date,
                'time'        => '10:00',
                'status'      => 'pending',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Berhasil'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
