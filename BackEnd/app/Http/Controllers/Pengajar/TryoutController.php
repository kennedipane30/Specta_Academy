<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Tryout;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\TryoutResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library dompdf sudah terinstal

class TryoutController extends Controller
{
    /**
     * 1. Halaman utama manajemen tryout (Pilih kelas untuk buat soal)
     */
    public function index()
    {
        $classes = ClassModel::all();
        return view('pengajar.tryout.index', compact('classes'));
    }

    /**
     * 2. Form buat soal & List Tryout per Kelas
     */
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

    /**
     * 3. Proses Import Soal dari CSV
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
            return back()->with('success', "Success! Tryout published with $count questions.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    /**
     * 4. Hapus Tryout
     */
    public function destroy($id)
    {
        $tryout = Tryout::findOrFail($id);
        $tryout->delete();

        return back()->with('success', 'Tryout successfully deleted!');
    }

    /**
     * 5. Lihat Nilai (Langkah 1: Pilih Kelas)
     */
    public function lihatNilai()
{
    // Langkah 1: Ambil semua kelas untuk dipilih
    $classes = ClassModel::all();
    return view('pengajar.tryout.nilai', compact('classes'));
}

public function detailNilai($class_id)
{
    // Langkah 2: Ambil detail nilai di kelas yang dipilih
    $class = ClassModel::findOrFail($class_id);
    $tryouts = Tryout::where('class_id', $class_id)
                     ->with(['results.user.student', 'questions'])
                     ->latest()
                     ->get();

    return view('pengajar.tryout.nilai_detail', compact('class', 'tryouts'));
}

    /**
     * 7. Export PDF Nilai per Kelas
     */
    public function exportPdf($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        $tryouts = Tryout::where('class_id', $class_id)
                         ->with(['results.user.student'])
                         ->get();

        $pdf = Pdf::loadView('pdf.rekap_nilai', compact('class', 'tryouts'));

        return $pdf->download('Score_Report_' . $class->program_name . '.pdf');
    }

    public function exportPdfSelected(Request $request)
        {
            // 1. Ambil ID hasil yang dicentang dari form
            $resultIds = $request->input('selected_results');

            if (!$resultIds) {
                return back()->with('error', 'Please select at least one student score to export.');
            }

            // 2. Ambil data Kelas (PENTING: Agar variabel $class tidak undefined)
            $class = ClassModel::findOrFail($request->class_id);

            // 3. Ambil data nilai berdasarkan ID yang dipilih dan kelompokkan berdasarkan judul Tryout
            $results = TryoutResult::whereIn('tryout_result_id', $resultIds)
                        ->with(['user.student', 'tryout'])
                        ->get()
                        ->groupBy('tryout.title');

            // 4. Kirim variabel 'results' dan 'class' ke view PDF
            $pdf = Pdf::loadView('pdf.rekap_nilai', compact('results', 'class'));

            return $pdf->download('Selected_Score_Report_' . $class->program_name . '.pdf');
        }

    public function lihatNilaiMobile() { return view('pengajar.tryout.nilai'); }
}
