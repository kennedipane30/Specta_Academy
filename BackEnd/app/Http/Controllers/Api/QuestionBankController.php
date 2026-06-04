<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

class QuestionBankController extends Controller 
{
    /**
     * 1. Ambil semua soal yang dibagikan oleh semua siswa
     * Endpoint: GET /api/question-bank
     */
    public function index() 
    {
        try {
            // Mengambil data beserta informasi nama pengunggah (User)
            // Relasi 'user' harus didefinisikan di model QuestionBank
            $data = QuestionBank::with('user:usersID,name')
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success', 
                'data' => $data
            ], 200);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data soal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. Proses Upload PDF oleh Siswa
     * Endpoint: POST /api/question-bank/upload
     */
    public function store(Request $request) 
    {
        // Validasi input dari Flutter/Frontend
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string',
            'file_pdf' => 'required|mimes:pdf|max:10240', // Maksimal 10MB
        ]);

        try {
            $user = Auth::user();

            // Proteksi: Hanya siswa dengan status enrollment 'active' yang bisa upload
            $is_active = Enrollment::where('user_id', $user->usersID)
                ->where('status', 'active')
                ->exists();

            if (!$is_active) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Akses Ditolak! Hanya siswa dengan status pendaftaran "active" yang bisa berbagi soal.'
                ], 403);
            }

            // Proses simpan file
            if ($request->hasFile('file_pdf')) {
                // Simpan ke folder: storage/app/public/question_bank
                $path = $request->file('file_pdf')->store('question_bank', 'public');

                // Simpan data ke database
                $qb = QuestionBank::create([
                    'user_id' => $user->usersID,
                    'title' => $request->title,
                    'subject' => $request->subject,
                    'file_path' => $path
                ]);

                return response()->json([
                    'status' => 'success', 
                    'message' => 'Berhasil! Soal Anda telah dibagikan ke Hub.', 
                    'data' => $qb
                ], 201);
            }

            return response()->json([
                'status' => 'error', 
                'message' => 'File PDF tidak ditemukan dalam request.'
            ], 400);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }
}