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
            $student = Student::where('user_id', $user->usersID)->first();

            if (!$student || is_null($student->class_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Silakan daftar kelas terlebih dahulu.',
                    'materials' => []
                ], 200);
            }

            // Ambil materi yang sesuai dengan class_id siswa
            $materials = Material::where('class_id', $student->class_id)
                        ->get(['material_id', 'title']);

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
                        ->where('student_id', $student->student_id)
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

            // ✨ MODIFIKASI: CEK KUOTA (MAKSIMAL 3)
            $existingCount = DedicatedTutor::where('student_id', $student->student_id)->count();
            if ($existingCount >= 3) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maaf, batas maksimal pengajuan tutor adalah 3 kali.'
                ], 403);
            }

            $dedicated = DedicatedTutor::create([
                'student_id'  => $student->student_id,
                'teacher_id'  => null, // Nanti diisi oleh Admin
                'material_id' => $request->material_id,
                'date'        => $request->date,
                'time'        => '10:00', // Default time
                'status'      => 'pending',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Request sent successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
