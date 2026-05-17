<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\PracticeQuestion;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; // ✨ Tambahkan ini untuk HTTP Client
use Illuminate\Support\Facades\Log;  // ✨ Tambahkan ini untuk Logging error

class PracticeQuestionController extends Controller
{
    public function index()
    {
        $assignments = TeacherAssignment::with('classModel')
                        ->where('user_id', Auth::user()->usersID)
                        ->get();

        return view('pengajar.Latihan.index', compact('assignments'));
    }

    public function selectPractice($class_id, $subject_name)
    {
        $access = TeacherAssignment::where('user_id', Auth::user()->usersID)
                    ->where('class_id', $class_id)
                    ->where('subject_name', $subject_name)
                    ->exists();

        if (!$access) abort(403);

        $class = ClassModel::findOrFail($class_id);

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
        fgetcsv($handle, 2000, ";");

        $questionsForSync = []; // ✨ Wadah untuk data yang akan dikirim ke Go

        while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
            if (!isset($row[0]) || empty(trim($row[0]))) continue;

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
                'explanation'    => $row[6] ?? null,
            ]);

            // ✨ Siapkan data untuk dikirim ke Microservice Go
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
                'explanation'         => $row[6] ?? null,
            ];
        }

        // ✨ PROSES SYNC: Kirim Bulk ke Go Practice Service (Port 8082)
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

    /**
     * ✨ FUNGSI HAPUS PER MINGGU (Dimodifikasi untuk Sync Hapus)
     */
    public function destroyByWeek($class_id, $subject, $week)
    {
        // 1. Hapus di Database Laravel
        PracticeQuestion::where('class_id', $class_id)
            ->where('subject', $subject)
            ->where('week', $week)
            ->delete();

        // 2. ✨ Hapus di Microservice Go
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
