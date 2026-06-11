<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\TryoutDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminTryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        $draftStatus = TryoutDraft::select('class_id', DB::raw('count(*) as total'))
                        ->groupBy('class_id')
                        ->get()
                        ->keyBy('class_id');

        // ✅ Ambil paket aktif dari Microservice Go
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $response = Http::get("$goUrl/api/tryouts");

        $activePackages = [];
        if ($response->successful()) {
            $data = $response->json();
            // Response format: {"status":"success", "data": [...]}
            $tryoutsData = $data['data'] ?? $data ?? [];

            // Transformasi data agar mudah digunakan di view
            foreach ($tryoutsData as $item) {
                // Cari class name berdasarkan class_id
                $class = $classes->firstWhere('class_id', $item['class_id'] ?? 0);

                $activePackages[] = (object) [
                    'tryout_id' => $item['tryout_id'] ?? 0,
                    'class_id' => $item['class_id'] ?? 0,
                    'title' => $item['title'] ?? 'Untitled',
                    'duration' => $item['duration'] ?? $item['duration_minutes'] ?? 0,
                    'total_questions' => $item['total_questions'] ?? 0,
                    'status' => $item['status'] ?? 'draft',
                    'is_active' => $item['is_active'] ?? false,
                    'class_name' => $class ? $class->program_name : 'Kelas #' . ($item['class_id'] ?? '?'),
                    'created_at' => $item['created_at'] ?? null,
                ];
            }
        }

        return view('admin.tryout.index', compact('classes', 'draftStatus', 'activePackages'));
    }

    public function reviewDrafts($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        $drafts = TryoutDraft::where('class_id', $class_id)
                    ->orderBy('subject_name')
                    ->orderBy('id')
                    ->get();

        return view('admin.tryout.review_drafts', compact('class', 'drafts'));
    }

    public function exportDraftCsv($class_id)
    {
        $drafts = TryoutDraft::where('class_id', $class_id)->get();
        $class = ClassModel::find($class_id);
        if ($drafts->isEmpty()) return back()->with('error', 'Data draf kosong.');

        $fileName = 'Draf_Soal_' . str_replace(' ', '_', $class->program_name) . '.csv';
        $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=$fileName"];

        return response()->stream(function() use($drafts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Mapel', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'Kunci', 'Pembahasan']);
            foreach ($drafts as $d) {
                fputcsv($file, [$d->subject_name, $d->question, $d->option_a, $d->option_b, $d->option_c, $d->option_d, $d->option_e, $d->correct_answer, $d->explanation]);
            }
            fclose($file);
        }, 200, $headers);
    }

    /**
     * ✅ PUBLISH KE MICROSERVICE GO
     */
    public function publishToMobile(Request $request)
    {
        $request->validate([
            'class_id' => 'required|integer',
            'title'    => 'required|string|max:255',
            'duration' => 'required|integer|min:1'
        ]);

        $classId = $request->class_id;
        $drafts  = TryoutDraft::where('class_id', $classId)->get();

        if ($drafts->isEmpty()) {
            return back()->with('error', 'Tidak ada draf soal untuk dipublish di kelas ini.');
        }

        DB::beginTransaction();
        try {
            $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

            $questionsForGo = [];
            foreach ($drafts as $d) {
                $cleanKey = substr(trim(strtoupper($d->correct_answer)), 0, 1);
                if (empty($cleanKey)) $cleanKey = 'A';

                $questionsForGo[] = [
                    'class_id'       => (int)$classId,
                    'subject_name'   => $d->subject_name,
                    'question'       => $d->question,
                    'option_a'       => $d->option_a,
                    'option_b'       => $d->option_b,
                    'option_c'       => $d->option_c,
                    'option_d'       => $d->option_d,
                    'option_e'       => $d->option_e,
                    'correct_answer' => $cleanKey,
                    'explanation'    => $d->explanation,
                ];
            }

            $payload = [
                'tryout' => [
                    'class_id'  => (int)$classId,
                    'title'     => trim($request->title),
                    'duration_minutes' => (int)$request->duration,
                    'total_questions' => count($questionsForGo),
                    'status'    => 'published',
                    'is_active' => true
                ],
                'questions' => $questionsForGo
            ];

            Log::info('Publishing tryout to Go service', ['payload' => $payload]);

            $response = Http::timeout(30)->post($goUrl . '/api/tryouts/sync', $payload);

            if (!$response->successful()) {
                throw new \Exception('Gagal sync ke microservice: ' . $response->body());
            }

            Log::info('Tryout published successfully', ['response' => $response->json()]);

            TryoutDraft::where('class_id', $classId)->delete();

            DB::commit();
            return redirect()->route('admin.tryout.index')->with('success', 'Berhasil mempublish paket (' . count($questionsForGo) . ' soal) ke aplikasi mobile!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Publish Error: " . $e->getMessage());
            return back()->with('error', 'Gagal Publish: ' . $e->getMessage());
        }
    }

    public function pilihKelas() {
        $classes = ClassModel::all();
        return view('admin.tryout.pilih_kelas', compact('classes'));
    }

    public function pilihTryout($class_id) {
        $class = ClassModel::findOrFail($class_id);

        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $response = Http::get("$goUrl/api/tryouts", ['class_id' => $class_id]);

        $tryouts = [];
        if ($response->successful()) {
            $data = $response->json();
            $tryouts = $data['data'] ?? $data ?? [];
        }

        return view('admin.tryout.pilih_paket', compact('class', 'tryouts'));
    }

    public function lihatNilai($tryout_id)
    {
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $response = Http::get("$goUrl/api/tryouts/history", ['tryout_id' => $tryout_id]);

        $results = [];
        if ($response->successful()) {
            $data = $response->json();
            $results = $data['data'] ?? $data ?? [];
        }

        $tryoutTitle = 'Tryout';
        $tryoutResponse = Http::get("$goUrl/api/tryouts");
        if ($tryoutResponse->successful()) {
            $tryoutsData = $tryoutResponse->json();
            $tryouts = $tryoutsData['data'] ?? $tryoutsData ?? [];
            foreach ($tryouts as $t) {
                if (($t['tryout_id'] ?? 0) == $tryout_id) {
                    $tryoutTitle = $t['title'] ?? 'Tryout';
                    break;
                }
            }
        }

        return view('admin.tryout.scores', compact('tryout_id', 'tryoutTitle', 'results'));
    }

    public function deleteDraft($id) {
        TryoutDraft::destroy($id);
        return back()->with('success', 'Draf berhasil dihapus.');
    }

    public function destroyPackage($tryout_id) {
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $response = Http::delete("$goUrl/api/tryouts/$tryout_id");

        if ($response->successful()) {
            return redirect()->route('admin.tryout.index')->with('success', 'Paket telah dihapus dari sistem.');
        }

        return back()->with('error', 'Gagal menghapus paket.');
    }
}
