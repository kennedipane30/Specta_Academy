<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use App\Models\Question;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // WAJIB ADA AGAR DB TIDAK MERAH
use Illuminate\Support\Facades\Validator;

class TryoutController extends Controller
{
    /**
     * Menampilkan form buat soal
     */
    public function buatSoal(Request $request)
    {
        $classId = $request->query('class_id');
        $class = ClassModel::findOrFail($classId);
        return view('pengajar.tryout.create', compact('class'));
    }

    /**
     * Memproses Import Soal dari CSV
     */
    public function importSoal(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'title'    => 'required|string|max:255',
            'file_csv' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $tryout = Tryout::create([
                'class_id' => $request->class_id,
                'title'    => $request->title,
                'duration' => $request->duration ?? 60,
            ]);

            $file = fopen($request->file('file_csv')->getRealPath(), 'r');
            fgetcsv($file, 2000, ";"); // Skip header baris 1

           $count = 0;
        // Gunakan delimiter ";" sesuai format Excel Indonesia kamu
        while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {

            // LOGIKA PEMBERSIH:
            // Jika kolom B (Pertanyaan) kosong, atau baris ini isinya cuma tanda ;;;; maka SKIP!
            if (!isset($row[1]) || empty(trim($row[1])) || trim($row[1]) == '') {
                continue;
            }

            Question::create([
                'tryout_id'      => $tryout->tryoutsID,
                'question'       => $row[1],
                'option_a'       => $row[2] ?? '-',
                'option_b'       => $row[3] ?? '-',
                'option_c'       => $row[4] ?? '-',
                'option_d'       => $row[5] ?? '-',
                'correct_answer' => trim(strtoupper($row[6] ?? 'A')),
                'explanation'    => $row[7] ?? null,
            ]);
            $count++;
        }
            fclose($file);

            DB::commit();
            return redirect()->back()->with('success', "Berhasil! Tryout diterbitkan dengan $count soal asli.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
};
