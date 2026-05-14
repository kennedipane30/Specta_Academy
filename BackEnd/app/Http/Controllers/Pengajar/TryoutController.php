<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\TeacherAssignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $existingSoal = TryoutSubmission::where('user_id', Auth::user()->usersID)
                        ->where('class_id', $class_id)
                        ->where('subject_name', $subject_name)
                        ->latest()
                        ->get();

        return view('pengajar.tryout.create', compact('class_id', 'subject_name', 'class', 'existingSoal'));
    }

    public function store(Request $request)
    {
        // 1. Identifikasi index mana saja yang ada isinya (Teks Pertanyaan ATAU Gambar Pertanyaan)
        $validIndices = [];
        if ($request->has('soal')) {
            foreach ($request->soal as $index => $data) {
                if (!empty($data['question']) || $request->hasFile("soal.$index.q_img")) {
                    $validIndices[] = $index;
                }
            }
        }

        // 2. Cek apakah jumlah soal yang valid minimal 5
        if (count($validIndices) < 5) {
            return back()->with('error', 'Gagal menyimpan! Anda wajib mengisi minimal 5 soal secara lengkap.')->withInput();
        }

        // 3. Buat aturan validasi dinamis hanya untuk index yang diisi saja
        $rules = [
            'class_id' => 'required',
            'subject_name' => 'required',
        ];

        foreach ($validIndices as $index) {
            $rules["soal.$index.explanation"] = ['required', 'regex:/^[a-zA-Z0-9\s.,?!-]+$/'];
            $rules["soal.$index.correct_answer"] = 'required';
            // Pastikan minimal ada teks opsi A atau gambar opsi A
            $rules["soal.$index.option_a"] = 'required_without:soal.' . $index . '.a_img';
        }

        $request->validate($rules, [
            'soal.*.explanation.required' => 'Pembahasan wajib diisi untuk semua soal yang dibuat.',
            'soal.*.explanation.regex' => 'Pembahasan hanya boleh berisi huruf, angka, dan tanda baca dasar.',
            'soal.*.option_a.required_without' => 'Setiap soal minimal harus memiliki Pilihan A.',
        ]);

        try {
            // 4. Proses Simpan hanya untuk index yang valid
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

                // --- PROSES UPLOAD GAMBAR ---
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
                    }
                }

                TryoutSubmission::create($input);
            }

            return redirect()->route('pengajar.tryout.index')->with('success', 'Paket soal berhasil diterbitkan ke Admin!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
}
