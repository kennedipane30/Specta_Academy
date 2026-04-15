<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\PracticeQuestion;
use Illuminate\Http\Request;

class PracticeQuestionController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        return view('pengajar.latihan.index', compact('classes'));
    }

    public function selectPractice($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        $practices = PracticeQuestion::where('class_id', $class_id)->get();

        return view('pengajar.latihan.pilih', compact('class', 'practices'));
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

        while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
            if (!isset($row[0]) || empty(trim($row[0]))) continue;

            PracticeQuestion::create([
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
        }

        fclose($handle);
        return back()->with('success', 'Practice Questions imported successfully!');
    }
}
