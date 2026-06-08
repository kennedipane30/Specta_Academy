<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\PracticeQuestion;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PracticeQuestionController extends Controller
{
    public function index()
    {
        $assignments = TeacherAssignment::with(['classModel', 'subject'])
                        ->where('user_id', Auth::user()->usersID)
                        ->get();

        return view('pengajar.Latihan.index', compact('assignments'));
    }

    public function selectPractice($class_id, $subject_id)
    {
        // 1. Cek akses pengajar berdasarkan ID Kelas dan ID Mapel
        $assignment = TeacherAssignment::with('subject')
                    ->where('user_id', Auth::user()->usersID)
                    ->where('class_id', $class_id)
                    ->where('subject_id', $subject_id)
                    ->first();

        // Jika tidak ditemukan penugasan, tampilkan 403
        if (!$assignment) abort(403);

        // 2. Ambil nama asli mata pelajaran dari relasi untuk filter soal
        $subject_name = $assignment->subject->material_name ?? 'N/A';

        $class = ClassModel::findOrFail($class_id);

        // 3. Ambil daftar latihan soal berdasarkan nama mapel
        $practices = PracticeQuestion::where('class_id', $class_id)
                        ->where('subject', $subject_name)
                        ->select('week', DB::raw('count(*) as total_soal'))
                        ->groupBy('week')
                        ->orderBy('week', 'asc')
                        ->get();

        return view('pengajar.Latihan.pilih', compact('class', 'subject_name', 'practices'));
    }

    public function storeCSV(Request $request, $class_id)
    {
        $request->validate([
            'subject'  => 'required',
            'week'     => 'required|integer|min:1|max:20',
            'file_csv' => 'required'
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");

        // Membuang baris pertama (Header/Nama Kolom)
        fgetcsv($handle, 2000, ";");

        $questionsForSync = [];

        while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
            if (!isset($row[0]) || empty(trim($row[0]))) continue;

            // ✨ MODIFIKASI: Menambahkan 'hint' ($row[6]) dan menggeser 'explanation' ($row[7])
            $q = PracticeQuestion::create([
                'class_id'       => $class_id,
                'subject'        => $request->subject,
                'week'           => $request->week,
                'question'       => $row[0],
                'option_a'       => $row[1] ?? '-',
                'option_b'       => $row[2] ?? '-',
                'option_c'       => $row[3] ?? '-',
                'option_d'       => $row[4] ?? '-',
                'correct_answer' => strtoupper(trim($row[5] ?? 'A')),
                'hint'           => $row[6] ?? null, // <--- TAMBAHAN HINT
                'explanation'    => $row[7] ?? null, // <--- BERGESER KE INDEX 7
            ]);

            // ✨ MODIFIKASI: Pastikan format payload ke Golang juga memiliki 'hint'
            $questionsForSync[] = [
                'practice_question_id' => $q->practice_question_id,
                'class_id'            => (int)$class_id,
                'subject'             => $request->subject,
                'week'                => (int)$request->week,
                'question'            => $row[0],
                'option_a'            => $row[1] ?? '-',
                'option_b'            => $row[2] ?? '-',
                'option_c'            => $row[3] ?? '-',
                'option_d'            => $row[4] ?? '-',
                'correct_answer'      => strtoupper(trim($row[5] ?? 'A')),
                'hint'                => $row[6] ?? null, // <--- TAMBAHAN HINT
                'explanation'         => $row[7] ?? null, // <--- BERGESER KE INDEX 7
            ];
        }

        try {
            $response = Http::post(env('GO_PRACTICE_URL') . '/practice/sync', $questionsForSync);
            if (!$response->successful()) {
                Log::error("Go Practice Service Gagal: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Koneksi ke Go Practice Service terputus.");
        }

        fclose($handle);
        return back()->with('success', 'Latihan soal berhasil diimport dan disinkronkan!');
    }

    public function destroyByWeek($class_id, $subject, $week)
    {
        PracticeQuestion::where('class_id', $class_id)
            ->where('subject', $subject)
            ->where('week', $week)
            ->delete();

        try {
            Http::delete(env('GO_PRACTICE_URL') . "/practice/$class_id/$week", [
                'subject' => $subject
            ]);
        } catch (\Exception $e) {
            Log::error("Gagal menghapus data di Go Practice Service");
        }

        return back()->with('success', "Semua soal minggu ke-$week berhasil dihapus!");
    }
}
