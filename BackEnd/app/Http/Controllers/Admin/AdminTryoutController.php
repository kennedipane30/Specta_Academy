<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\ClassModel;
use App\Models\Question;
use App\Models\Tryout;
use App\Models\User; // ✨ Pastikan import model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminTryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all(); // Diambil dari DB Utama

        // 1. Ambil submissions dari DB Tryout (Tanpa 'with' karena beda DB)
        $submissions = TryoutSubmission::latest()->get();

        // 2. ✨ MANUAL HYDRATION (Menggabungkan data antar Database)
        $userIds = $submissions->pluck('user_id')->unique();
        $classIds = $submissions->pluck('class_id')->unique();

        // Ambil User dan Class dari Database Utama
        $users = User::whereIn('usersID', $userIds)->get()->keyBy('usersID');
        $allClasses = ClassModel::whereIn('class_id', $classIds)->get()->keyBy('class_id');

        // Pasangkan secara manual ke dalam collection
        foreach ($submissions as $sub) {
            $sub->setRelation('user', $users->get($sub->user_id));
            $sub->setRelation('classModel', $allClasses->get($sub->class_id));
        }

        // 3. Ambil statistik soal aktif
        $activeTryouts = Question::select('class_id', DB::raw('count(*) as total'))
                        ->groupBy('class_id')
                        ->get();

        // Pasangkan data kelas ke statistik
        foreach ($activeTryouts as $at) {
            $at->setRelation('classModel', $allClasses->get($at->class_id));
        }

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
        fgetcsv($handle, 2000, ","); // Skip header

        DB::beginTransaction();
        try {
            $tryout = Tryout::updateOrCreate(
                ['class_id' => $request->class_id],
                [
                    'title' => 'Tryout Akbar ' . $class->program_name,
                    'duration' => 60,
                    'is_active' => true
                ]
            );

            $questionsForGo = [];
            $count = 0;
            while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
                if (empty($row[1]) && empty($row[2])) continue;

                $q = Question::create([
                    'class_id'       => $request->class_id,
                    'tryout_id'      => $tryout->tryout_id,
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

                // ✨ SESUAIKAN: Format array untuk Go Tryout Service
                $questionsForGo[] = [
                    'tryout_id'      => (int)$tryout->tryout_id,
                    'class_id'       => (int)$request->class_id,
                    'question'       => $row[1] ?? '-',
                    'question_image' => $row[2] ?: "",
                    'option_a'       => $row[3] ?? '-',
                    'option_a_image' => $row[4] ?: "",
                    'option_b'       => $row[5] ?? '-',
                    'option_b_image' => $row[6] ?: "",
                    'option_c'       => $row[7] ?? '-',
                    'option_c_image' => $row[8] ?: "",
                    'option_d'       => $row[9] ?? '-',
                    'option_d_image' => $row[10] ?: "",
                    'correct_answer' => $row[11] ?? 'A',
                    'explanation'    => $row[12] ?? '-',
                ];
                $count++;
            }
            fclose($handle);
            DB::commit();

            // ✨ SYNC KE MICROSERVICE GO
            try {
                $response = Http::post(env('GO_TRYOUT_URL') . '/api/tryouts/sync', [
                    'tryout' => [
                        'tryout_id' => (int)$tryout->tryout_id,
                        'class_id'  => (int)$request->class_id,
                        'title'     => $tryout->title,
                        'duration'  => 60
                    ],
                    'questions' => $questionsForGo
                ]);

                if (!$response->successful()) {
                    Log::error("Tryout Service Gagal: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Koneksi ke Tryout Service terputus.");
            }

            return back()->with('success', "Berhasil! $count soal tersinkron ke Service Tryout.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroyPackage($class_id)
    {
        Tryout::where('class_id', $class_id)->delete();
        Question::where('class_id', $class_id)->delete();
        return back()->with('success', 'Paket Tryout berhasil dihapus.');
    }
}
