<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminTryoutController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();

        // ✅ Gunakan koneksi pgsql_tryout (database microservice tryout)
        $draftStatus = [];
        try {
            $drafts = DB::connection('pgsql_tryout')->table('tryout_drafts')->get();

            foreach ($drafts as $draft) {
                $classId = $draft->class_id;
                if (!isset($draftStatus[$classId])) {
                    $draftStatus[$classId] = (object) ['total' => 0];
                }
                $draftStatus[$classId]->total++;
            }

            // Debug: log jumlah draft
            Log::info('Jumlah draft dari microservice tryout: ' . $drafts->count());

        } catch (\Exception $e) {
            Log::error('Gagal mengambil draft dari database microservice tryout: ' . $e->getMessage());
            $draftStatus = [];
        }

        // Ambil paket aktif dari Microservice Go
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $activePackages = [];
        $serviceError = false;

        try {
            $response = Http::timeout(5)->get($goUrl . '/api/tryouts');

            if ($response->successful()) {
                $data = $response->json();
                $tryoutsData = $data['data'] ?? $data ?? [];

                foreach ($tryoutsData as $item) {
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
            } else {
                $serviceError = true;
            }

        } catch (\Exception $e) {
            $serviceError = true;
            Log::warning('Go Tryout Service tidak tersedia: ' . $e->getMessage());
        }

        return view('admin.tryout.index', compact('classes', 'draftStatus', 'activePackages', 'serviceError'));
    }

    public function reviewDrafts($class_id)
    {
        $class = ClassModel::findOrFail($class_id);

        // ✅ Gunakan koneksi pgsql_tryout
        $drafts = DB::connection('pgsql_tryout')->table('tryout_drafts')
                    ->where('class_id', $class_id)
                    ->orderBy('subject_name')
                    ->orderBy('id')
                    ->get();

        return view('admin.tryout.review_drafts', compact('class', 'drafts'));
    }

    public function exportDraftCsv($class_id)
    {
        // ✅ Gunakan koneksi pgsql_tryout
        $drafts = DB::connection('pgsql_tryout')->table('tryout_drafts')
                    ->where('class_id', $class_id)
                    ->get();

        $class = ClassModel::find($class_id);
        if ($drafts->isEmpty()) return back()->with('error', 'Data draf kosong.');

        $fileName = 'Draf_Soal_' . str_replace(' ', '_', $class->program_name) . '.csv';

        return response()->stream(function() use($drafts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Mapel', 'Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'Kunci', 'Pembahasan']);
            foreach ($drafts as $d) {
                fputcsv($file, [
                    $d->subject_name,
                    $d->question,
                    $d->option_a,
                    $d->option_b,
                    $d->option_c,
                    $d->option_d,
                    $d->option_e,
                    $d->correct_answer,
                    $d->explanation
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ]);
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
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        // ✅ Ambil draft dari database microservice tryout
        $drafts = DB::connection('pgsql_tryout')->table('tryout_drafts')
                    ->where('class_id', $classId)
                    ->get();

        if ($drafts->isEmpty()) {
            return back()->with('error', 'Tidak ada draf soal untuk dipublish.');
        }

        try {
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

            // Hapus draft dari database setelah publish
            DB::connection('pgsql_tryout')->table('tryout_drafts')
                ->where('class_id', $classId)
                ->delete();

            return redirect()->route('admin.tryout.index')->with('success',
                'Berhasil publish paket (' . count($questionsForGo) . ' soal) ke aplikasi mobile!');

        } catch (\Exception $e) {
            Log::error("Publish Error: " . $e->getMessage());
            return back()->with('error', 'Gagal Publish: ' . $e->getMessage());
        }
    }

    public function pilihKelas()
    {
        $classes = ClassModel::all();
        return view('admin.tryout.pilih_kelas', compact('classes'));
    }

    public function pilihTryout($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        $tryouts = [];
        $serviceError = false;

        try {
            $response = Http::timeout(5)->get($goUrl . '/api/tryouts', ['class_id' => $class_id]);

            if ($response->successful()) {
                $data = $response->json();
                $tryouts = $data['data'] ?? $data ?? [];
            } else {
                $serviceError = true;
            }

        } catch (\Exception $e) {
            $serviceError = true;
            Log::warning('Go Tryout Service tidak tersedia: ' . $e->getMessage());
        }

        return view('admin.tryout.pilih_paket', compact('class', 'tryouts', 'serviceError'));
    }

    public function lihatNilai($tryout_id)
    {
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        $results = [];
        $tryoutTitle = 'Tryout';
        $serviceError = false;

        try {
            $response = Http::timeout(5)->get($goUrl . '/api/tryouts/history', ['tryout_id' => $tryout_id]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['data'] ?? $data ?? [];
            } else {
                $serviceError = true;
            }

            $tryoutResponse = Http::timeout(5)->get($goUrl . '/api/tryouts');
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

        } catch (\Exception $e) {
            $serviceError = true;
            Log::warning('Go Tryout Service tidak tersedia: ' . $e->getMessage());
        }

        return view('admin.tryout.scores', compact('tryout_id', 'tryoutTitle', 'results', 'serviceError'));
    }

    public function deleteDraft($id)
    {
        try {
            DB::connection('pgsql_tryout')->table('tryout_drafts')->where('id', $id)->delete();
            return back()->with('success', 'Draf berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Delete Draft Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus draf.');
        }
    }

    public function destroyPackage($tryout_id)
    {
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        try {
            $response = Http::timeout(10)->delete($goUrl . '/api/tryouts/' . $tryout_id);

            if ($response->successful()) {
                return redirect()->route('admin.tryout.index')->with('success', 'Paket telah dihapus dari sistem.');
            } else {
                $errorMsg = $response->json()['error'] ?? $response->body();
                return back()->with('error', 'Gagal menghapus paket: ' . $errorMsg);
            }

        } catch (\Exception $e) {
            Log::error("Delete Package Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus paket: ' . $e->getMessage());
        }
    }
}
