<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuestionBankController extends Controller {

    // 1. Ambil semua soal yang dibagikan
    public function index() {
        $data = QuestionBank::with('user:usersID,name')->latest()->get();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    // 2. Proses Upload PDF
    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string',
            'file_pdf' => 'required|mimes:pdf|max:10240', // Max 10MB
        ]);

        // Cek apakah siswa sudah aktif (pendaftaran diterima)
        $is_active = Enrollment::where('user_id', Auth::id())->where('status', 'active')->exists();
        if (!$is_active) {
            return response()->json(['status' => 'error', 'message' => 'Hanya siswa aktif yang bisa berbagi soal.'], 403);
        }

        if ($request->hasFile('file_pdf')) {
            $path = $request->file('file_pdf')->store('question_bank', 'public');

            $qb = QuestionBank::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'subject' => $request->subject,
                'file_path' => $path
            ]);

            return response()->json(['status' => 'success', 'message' => 'Soal berhasil dibagikan!', 'data' => $qb], 201);
        }
    }
}
