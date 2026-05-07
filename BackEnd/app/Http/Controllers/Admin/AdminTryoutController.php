<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\ClassModel;
use Illuminate\Http\Request;

class AdminTryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        // Mengambil semua kiriman soal beserta info pengajar
        $submissions = TryoutSubmission::with(['user', 'classModel'])->latest()->get();

        return view('admin.tryout.index', compact('submissions', 'classes'));
    }

    public function exportCsv($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        $questions = TryoutSubmission::where('class_id', $class_id)->get();

        $fileName = 'Master_Soal_' . str_replace(' ', '_', $class->program_name) . '.csv';
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
}
