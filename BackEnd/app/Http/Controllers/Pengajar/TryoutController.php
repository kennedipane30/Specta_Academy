<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\TeacherAssignment;
use App\Models\ClassModel; // Import ClassModel
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
    $class = \App\Models\ClassModel::findOrFail($class_id);

    // AMBIL DATA RIWAYAT: Pastikan menggunakan usersID
    $existingSoal = \App\Models\TryoutSubmission::where('user_id', Auth::user()->usersID)
                    ->where('class_id', $class_id)
                    ->where('subject_name', $subject_name)
                    ->latest()
                    ->get();

    return view('pengajar.tryout.create', compact('class_id', 'subject_name', 'class', 'existingSoal'));
}

    public function store(Request $request)
    {
        // 1. Filter hanya soal yang diisi (ada teks pertanyaan atau gambar)
        $submittedSoal = collect($request->soal)->filter(function ($item, $index) use ($request) {
            return !empty($item['question']) || $request->hasFile("soal.$index.q_img");
        });

        // 2. Cek apakah jumlah soal yang diisi memenuhi minimal 5
        if ($submittedSoal->count() < 5) {
            return back()->with('error', 'Gagal! Anda wajib mengisi minimal 5 soal secara lengkap.')->withInput();
        }

        // 3. Validasi hanya pada soal yang sudah difilter
        $request->merge(['soal' => $submittedSoal->all()]);
        $request->validate([
            'class_id' => 'required',
            'subject_name' => 'required',
            'soal.*.explanation' => ['required', 'regex:/^[a-zA-Z0-9\s.,?!-]+$/'],
            'soal.*.question' => 'required_without:soal.*.q_img',
            'soal.*.option_a' => 'required', // Opsi minimal harus ada teks
        ], [
            'soal.*.explanation.regex' => 'Pembahasan soal hanya boleh berisi huruf, angka, dan tanda baca.',
        ]);

        try {
            foreach ($request->soal as $index => $data) {
                // Proses penyimpanan data seperti sebelumnya
                $input = [
                    'user_id' => Auth::user()->usersID,
                    'class_id' => (int) $request->class_id,
                    'subject_name' => $request->subject_name,
                    'question' => $data['question'] ?? null,
                    'option_a' => $data['option_a'] ?? null,
                    'option_b' => $data['option_b'] ?? null,
                    'option_c' => $data['option_c'] ?? null,
                    'option_d' => $data['option_d'] ?? null,
                    'correct_answer' => $data['correct_answer'],
                    'explanation' => $data['explanation'],
                ];

                $imageFields = ['question_image'=>'q_img', 'option_a_image'=>'a_img', 'option_b_image'=>'b_img', 'option_c_image'=>'c_img', 'option_d_image'=>'d_img'];
                foreach ($imageFields as $dbCol => $formKey) {
                    // Cari file berdasarkan index asli, bukan index baru setelah filter
                    $originalIndex = $submittedSoal->keys()[$index];
                    if ($request->hasFile("soal.$originalIndex.$formKey")) {
                        $file = $request->file("soal.$originalIndex.$formKey");
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->storeAs('public/tryout/images', $filename);
                        $input[$dbCol] = $filename;
                    }
                }
                TryoutSubmission::create($input);
            }
            return redirect()->route('pengajar.tryout.index')->with('success', 'Paket soal berhasil dikirim ke Admin!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan teknis: ' . $e->getMessage())->withInput();
        }
    }
}
