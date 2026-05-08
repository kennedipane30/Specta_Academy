<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        return view('pengajar.tryout.create', compact('class_id', 'subject_name'));
    }

    public function store(Request $request)
    {
        // 1. Validasi: Penjelasan hanya Huruf & Angka, Soal minimal 5
        $request->validate([
            'class_id' => 'required',
            'subject_name' => 'required',
            'soal' => 'required|array|min:5',
            'soal.*.explanation' => ['required', 'regex:/^[a-zA-Z0-9\s.]+$/'],
        ], [
            'soal.*.explanation.regex' => 'Pembahasan soal hanya boleh berisi huruf dan angka saja.'
        ]);

        try {
            foreach ($request->soal as $index => $data) {
                // Simpan jika minimal Pertanyaan Teks atau Gambar ada
                if (!empty($data['question']) || $request->hasFile("soal.$index.q_img")) {

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

                    // --- UPLOAD GAMBAR ---
                    $fields = ['question_image'=>'q_img', 'option_a_image'=>'a_img', 'option_b_image'=>'b_img', 'option_c_image'=>'c_img', 'option_d_image'=>'d_img'];
                    foreach ($fields as $dbCol => $formKey) {
                        if ($request->hasFile("soal.$index.$formKey")) {
                            $file = $request->file("soal.$index.$formKey");
                            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                            $file->storeAs('public/tryout/images', $filename);
                            $input[$dbCol] = $filename;
                        }
                    }

                    TryoutSubmission::create($input);
                }
            }

            return redirect()->route('pengajar.tryout.index')->with('success', 'Paket soal berhasil dikirim ke Admin!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage())->withInput();
        }
    }
}
