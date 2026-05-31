<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TryoutSubmission;
use App\Models\ClassModel;
use App\Models\Question;
use App\Models\Tryout;
use App\Models\TryoutResult;
use App\Models\TryoutDraft; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminTryoutController extends Controller
{
    /**
     * 1. DASHBOARD MONITORING TRYOUT
     */
    public function index()
    {
        $classes = ClassModel::all();
        $draftStatus = TryoutDraft::select('class_id', DB::raw('count(*) as total'))
                        ->groupBy('class_id')
                        ->get()
                        ->keyBy('class_id');

        $activePackages = Tryout::with('classModel')->withCount('questions')->latest()->get();

        return view('admin.tryout.index', compact('classes', 'draftStatus', 'activePackages'));
    }

    /**
     * 2. REVIEW DRAF SOAL GURU
     */
    public function reviewDrafts($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        $drafts = TryoutDraft::where('class_id', $class_id)->orderBy('subject_name')->get();
        return view('admin.tryout.review_drafts', compact('class', 'drafts'));
    }

    /**
     * 3. DOWNLOAD DRAF (EXPORT CSV)
     */
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
     * 4. GABUNGKAN & PUBLISH KE MOBILE
     */
    public function publishToMobile(Request $request)
    {
        $request->validate(['class_id' => 'required', 'title' => 'required', 'duration' => 'required']);
        $classId = $request->class_id;
        $drafts = TryoutDraft::where('class_id', $classId)->get();
        if ($drafts->isEmpty()) return back()->with('error', 'Tidak ada draf soal.');

        DB::beginTransaction();
        try {
            // A. Buat Header di Laravel
            $tryout = Tryout::create([
                'class_id' => $classId,
                'title' => $request->title,
                'duration_minutes' => (int)$request->duration, // Sesuaikan nama kolom DB Anda
                'status' => 'published',
                'is_active' => true
            ]);

            $questionsForGo = [];
            foreach ($drafts as $index => $d) {
                // B. Simpan di DB Laravel Lokal
                Question::create([
                    'tryout_id' => $tryout->tryout_id,
                    'class_id' => $classId,
                    'subject' => $d->subject_name,
                    'question' => $d->question,
                    'option_a' => $d->option_a,
                    'option_b' => $d->option_b,
                    'option_c' => $d->option_c,
                    'option_d' => $d->option_d,
                    'option_e' => $d->option_e,
                    'correct_answer' => $d->correct_answer,
                    'explanation' => $d->explanation,
                ]);

                // C. Siapkan data untuk Go Service
                $questionsForGo[] = [
                    'tryout_id' => (int)$tryout->tryout_id,
                    'class_id' => (int)$classId,
                    'subject_name' => $d->subject_name,
                    'question' => $d->question,
                    'option_a' => $d->option_a,
                    'option_b' => $d->option_b,
                    'option_c' => $d->option_c,
                    'option_d' => $d->option_d,
                    'option_e' => $d->option_e,
                    'correct_answer' => $d->correct_answer,
                    'explanation' => $d->explanation,
                ];
            }

            // D. Sinkronisasi ke GO Service Port 9003
            Http::post(env('GO_TRYOUT_URL') . '/api/tryouts/sync', [
                'tryout' => [
                    'tryout_id' => (int)$tryout->tryout_id,
                    'class_id'  => (int)$classId,
                    'title'     => $tryout->title,
                    'duration'  => (int)$request->duration,
                    'is_active' => true
                ],
                'questions' => $questionsForGo
            ]);

            TryoutDraft::where('class_id', $classId)->delete();
            DB::commit();
            return redirect()->route('admin.tryout.index')->with('success', 'Berhasil Publish Paket!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Publish Error: " . $e->getMessage());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * 5. REKAP NILAI SISWA (Pilih Kelas -> Pilih Paket -> Lihat Nilai)
     */
    public function pilihKelas() {
        $classes = ClassModel::all();
        return view('admin.tryout.pilih_kelas', compact('classes'));
    }

    public function pilihTryout($class_id) {
        $class = ClassModel::findOrFail($class_id);
        $tryouts = Tryout::where('class_id', $class_id)->get();
        return view('admin.tryout.pilih_paket', compact('class', 'tryouts'));
    }

    // ✨ FUNGSI YANG DIPERBAIKI (Fix Undefined Variable $tryout)
    public function lihatNilai($tryout_id) {
        // 1. Ambil data Paket Tryout untuk Judul Halaman
        $tryout = Tryout::where('tryout_id', $tryout_id)->first();

        if (!$tryout) {
            return redirect()->route('admin.scores.index')->with('error', 'Paket Tryout tidak ditemukan.');
        }

        // 2. Ambil semua hasil nilai siswa
        $results = TryoutResult::where('tryout_id', $tryout_id)->latest()->get();

        // 3. Gabungkan dengan data User (Siswa)
        foreach ($results as $res) {
            $res->user_data = User::where('usersID', $res->user_id)->first();
        }

        // 4. Kirim $tryout dan $results ke view
        return view('admin.tryout.scores', compact('tryout', 'results'));
    }

    /**
     * 6. HAPUS DATA
     */
    public function deleteDraft($id) {
        TryoutDraft::destroy($id);
        return back()->with('success', 'Draf dihapus.');
    }

    public function destroyPackage($tryout_id) {
        Tryout::where('tryout_id', $tryout_id)->delete();
        Question::where('tryout_id', $tryout_id)->delete();
        return back()->with('success', 'Paket dihapus dari sistem.');
    }
}