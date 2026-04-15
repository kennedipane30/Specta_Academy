<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use App\Models\Question;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        return view('pengajar.tryout.index', compact('classes'));
    }

    public function buatSoal($class_id)
    {
        $class = ClassModel::findOrFail($class_id);

        // AMBIL DAFTAR TRYOUT UNTUK KELAS INI (Beserta jumlah soalnya)
        $tryouts = Tryout::where('class_id', $class_id)
                         ->withCount('questions')
                         ->latest()
                         ->get();

        return view('pengajar.tryout.create', compact('class', 'tryouts'));
    }

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
            fgetcsv($file, 2000, ";");

            $count = 0;
            while (($row = fgetcsv($file, 2000, ";")) !== FALSE) {
                if (!isset($row[1]) || empty(trim($row[1]))) continue;

                Question::create([
                    'tryout_id'      => $tryout->tryout_id,
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
            return back()->with('success', "Berhasil! Tryout diterbitkan dengan $count soal.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // FUNGSI HAPUS TRYOUT
    public function destroy($id)
    {
        $tryout = Tryout::findOrFail($id);
        $tryout->delete(); // Ini akan menghapus questions juga jika onDelete('cascade') sudah di-set di migration

        return back()->with('success', 'Tryout Berhasil Dihapus!');
    }

    public function lihatNilai() { return view('pengajar.tryout.nilai'); }
}
