<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\ClassModel;
use App\Models\Question;
use App\Models\Tryout; // ✨ Wajib Import Model Tryout
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        $submissions = TryoutSubmission::with(['user', 'classModel'])->latest()->get();

        $activeTryouts = Question::select('class_id', DB::raw('count(*) as total'))
                        ->groupBy('class_id')
                        ->with('classModel')
                        ->get();

        return view('admin.tryout.index', compact('submissions', 'classes', 'activeTryouts'));
    }

    public function exportCsv($class_id)
    {
        $questions = TryoutSubmission::where('class_id', $class_id)->get();
        $class = ClassModel::find($class_id);
        $fileName = 'Master_Kurasi_' . str_replace(' ', '_', $class->program_name ?? 'Kelas') . '.csv';

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

    public function uploadMaster(Request $request)
    {
        $request->validate(['class_id' => 'required', 'file_csv' => 'required|mimes:csv,txt']);

        $class = ClassModel::find($request->class_id);
        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");
        fgetcsv($handle, 2000, ",");

        DB::beginTransaction();
        try {
            // ✨ PERBAIKAN UTAMA: Buat Induk Paket Tryout agar muncul di Mobile
            $tryout = Tryout::updateOrCreate(
                ['class_id' => $request->class_id],
                [
                    'title' => 'Tryout Akbar ' . $class->program_name,
                    'duration' => 60, // Default 60 menit
                    'is_active' => true
                ]
            );

            $count = 0;
            while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
                if (empty($row[1]) && empty($row[2])) continue;

                Question::create([
                    'class_id'       => $request->class_id,
                    'tryout_id'      => $tryout->tryout_id, // ✨ Hubungkan ke Paket Tryout
                    'question'       => $row[1] ?? '-',
                    'question_image' => $row[2] ?: null,
                    'option_a'       => $row[3] ?? '-',
                    'option_a_image' => $row[4] ?: null,
                    'option_b'       => $row[5] ?? '-',
                    'option_b_image' => $row[6] ?: null,
                    'option_c'       => $row[7] ?? '-',
                    'option_c_image' => $row[8] ?: null,
                    'option_d'       => $row[9] ?? '-',
                    'option_d_image' => $row[10] ?: null,
                    'correct_answer' => $row[11] ?? 'A',
                    'explanation'    => $row[12] ?? '-',
                ]);
                $count++;
            }
            fclose($handle);
            DB::commit();
            return back()->with('success', "Berhasil! $count soal dan 1 Paket Tryout telah aktif di Mobile.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroyPackage($class_id)
    {
        // ✨ Hapus Paket Tryout dan Soal-soalnya
        Tryout::where('class_id', $class_id)->delete();
        Question::where('class_id', $class_id)->delete();
        return back()->with('success', 'Paket Tryout berhasil dihapus dari Mobile.');
    }
}
