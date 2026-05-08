<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\ClassModel;
use App\Models\Question; // Pastikan model Question ada
use Illuminate\Http\Request;

class AdminTryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        $submissions = TryoutSubmission::with(['user', 'classModel'])->latest()->get();
        return view('admin.tryout.index', compact('submissions', 'classes'));
    }

    public function exportCsv($class_id)
    {
        $questions = TryoutSubmission::where('class_id', $class_id)->get();
        $fileName = 'Master_Soal_Kelas_'.$class_id.'.csv';

        $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$fileName"];
        $columns = ['No', 'Pertanyaan', 'Gbr_Soal', 'Opsi A', 'Gbr_A', 'Opsi B', 'Gbr_B', 'Opsi C', 'Gbr_C', 'Opsi D', 'Gbr_D', 'Kunci', 'Pembahasan'];

        $callback = function() use($questions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $no = 1;
            foreach ($questions as $q) {
                fputcsv($file, [
                    $no++, $q->question, $q->question_image,
                    $q->option_a, $q->option_a_image,
                    $q->option_b, $q->option_b_image,
                    $q->option_c, $q->option_c_image,
                    $q->option_d, $q->option_d_image,
                    $q->correct_answer, $q->explanation
                ]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    // ✨ FUNGSI UNTUK PROSES UPLOAD CSV MASTER KE MOBILE
    public function uploadMaster(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'file_csv' => 'required|mimes:csv,txt'
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");
        fgetcsv($handle, 2000, ","); // Skip Header

        try {
            while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
                if (empty($row[1]) && empty($row[2])) continue;

                // Simpan ke tabel Question aplikasi Mobile
                \App\Models\Question::create([
                    'class_id'       => $request->class_id,
                    'question_text'  => $row[1] ?? '-',
                    'question_image' => $row[2] ?? null,
                    'option_a'       => $row[3] ?? '-',
                    'option_a_image' => $row[4] ?? null,
                    'option_b'       => $row[5] ?? '-',
                    'option_b_image' => $row[6] ?? null,
                    'option_c'       => $row[7] ?? '-',
                    'option_c_image' => $row[8] ?? null,
                    'option_d'       => $row[9] ?? '-',
                    'option_d_image' => $row[10] ?? null,
                    'correct_answer' => $row[11] ?? 'A',
                    'explanation'    => $row[12] ?? '-',
                ]);
            }
            fclose($handle);
            return back()->with('success', 'Sukses mempublikasikan soal ke aplikasi Mobile!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}   
