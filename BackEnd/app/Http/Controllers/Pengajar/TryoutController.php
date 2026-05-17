<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\TeacherAssignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // ✨ Tambahkan ini
use Illuminate\Support\Facades\Log;  // ✨ Tambahkan ini

class TryoutController extends Controller
{
    public function index()
    {
        $assignments = TeacherAssignment::with('classModel')
                        ->where('user_id', Auth::user()->usersID)->get();
        return view('pengajar.tryout.index', compact('assignments'));
    }

    public function create($class_id, $subject_name)
    {
        $class = ClassModel::findOrFail($class_id);

        // Ambil data riwayat khusus pengajar ini untuk subjek ini
        // Laravel akan mencari di koneksi 'pgsql_tryout' (specta_tryout)
        $existingSoal = TryoutSubmission::where('user_id', Auth::user()->usersID)
                        ->where('class_id', $class_id)
                        ->where('subject_name', $subject_name)
                        ->latest()
                        ->get();

        return view('pengajar.tryout.create', compact('class_id', 'subject_name', 'class', 'existingSoal'));
    }

    public function store(Request $request)
    {
        $validIndices = [];
        if ($request->has('soal')) {
            foreach ($request->soal as $index => $data) {
                if (!empty($data['question']) || $request->hasFile("soal.$index.q_img")) {
                    $validIndices[] = $index;
                }
            }
        }

        if (count($validIndices) < 5) {
            return back()->with('error', 'Gagal menyimpan! Anda wajib mengisi minimal 5 soal secara lengkap.')->withInput();
        }

        $rules = [
            'class_id' => 'required',
            'subject_name' => 'required',
        ];

        foreach ($validIndices as $index) {
            $rules["soal.$index.explanation"] = ['required', 'regex:/^[a-zA-Z0-9\s.,?!-]+$/'];
            $rules["soal.$index.correct_answer"] = 'required';
            $rules["soal.$index.option_a"] = 'required_without:soal.' . $index . '.a_img';
        }

        $request->validate($rules, [
            'soal.*.explanation.required' => 'Pembahasan wajib diisi untuk semua soal yang dibuat.',
            'soal.*.option_a.required_without' => 'Setiap soal minimal harus memiliki Pilihan A.',
        ]);

        try {
            $syncData = []; // ✨ Tampung data untuk sinkronisasi ke Go

            foreach ($validIndices as $index) {
                $data = $request->soal[$index];

                $input = [
                    'user_id'        => Auth::user()->usersID,
                    'class_id'       => (int) $request->class_id,
                    'subject_name'   => $request->subject_name,
                    'question'       => $data['question'] ?? null,
                    'option_a'       => $data['option_a'] ?? null,
                    'option_b'       => $data['option_b'] ?? null,
                    'option_c'       => $data['option_c'] ?? null,
                    'option_d'       => $data['option_d'] ?? null,
                    'correct_answer' => $data['correct_answer'],
                    'explanation'    => $data['explanation'],
                ];

                $imageMap = [
                    'question_image' => 'q_img',
                    'option_a_image' => 'a_img',
                    'option_b_image' => 'b_img',
                    'option_c_image' => 'c_img',
                    'option_d_image' => 'd_img'
                ];

                foreach ($imageMap as $dbCol => $fileKey) {
                    if ($request->hasFile("soal.$index.$fileKey")) {
                        $file = $request->file("soal.$index.$fileKey");
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('public/tryout/images', $filename);
                        $input[$dbCol] = $filename;
                    } else {
                        $input[$dbCol] = null;
                    }
                }

                $submission = TryoutSubmission::create($input);
                $input['id'] = $submission->id; // Ambil ID hasil simpan
                $syncData[] = $input; // Masukkan ke array sync
            }

            // ✨ PROSES SYNC: Kirim ke Go Tryout Service
            try {
                $response = Http::post(env('GO_TRYOUT_URL') . '/tryouts/submissions/sync', $syncData);
                if (!$response->successful()) {
                    Log::error("Go Tryout Service Sync Error: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Koneksi ke Go Tryout Service terputus saat sync submission.");
            }

            return redirect()->route('pengajar.tryout.index')->with('success', 'Paket soal berhasil diterbitkan ke Admin dan disinkronkan!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
}
