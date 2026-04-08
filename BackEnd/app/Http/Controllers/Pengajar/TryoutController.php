<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use App\Models\Question;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TryoutController extends Controller
{
    /**
     * 1. Menampilkan daftar kelas (Pintu masuk utama alur kartu)
     */
    public function index()
    {
        $classes = ClassModel::all();
        return view('pengajar.tryout.index', compact('classes'));
    }

    /**
     * 2. Menampilkan form buat soal (Setelah pilih kartu kelas)
     */
    public function buatSoal($class_id)
    {
        // Sekarang menerima parameter ID langsung dari URL
        $class = ClassModel::findOrFail($class_id);
        return view('pengajar.tryout.create', compact('class'));
    }

    /**
     * 3. Memproses Import Soal dari CSV
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
            fgetcsv($file, 2000, ";"); // Skip header

            $count = 0;
            while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
                if (!isset($row[1]) || empty(trim($row[1]))) {
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

    /**
     * MODIFIKASI: Menambahkan fungsi lihatNilai agar route 'pengajar.tryout.nilai' berfungsi
     */
    public function lihatNilai()
    {
        return view('pengajar.tryout.nilai');
    }
}
