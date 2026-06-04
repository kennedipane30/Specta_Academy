<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        // Menghitung jumlah draf soal per kelas
        $draftStatus = TryoutDraft::select('class_id', DB::raw('count(*) as total'))
                        ->groupBy('class_id')
                        ->get()
                        ->keyBy('class_id');

        $activePackages = Tryout::with('classModel')->withCount('questions')->latest()->get();

        return view('admin.tryout.index', compact('classes', 'draftStatus', 'activePackages'));
    }

    /**
     * 2. REVIEW DRAF SOAL GURU (Melihat 15 soal gabungan sebelum publish)
     */
    public function reviewDrafts($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        // Mengambil semua draf dari berbagai mapel di kelas yang sama
        $drafts = TryoutDraft::where('class_id', $class_id)
                    ->orderBy('subject_name')
                    ->orderBy('id')
                    ->get();

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
     * ✨ MODIFIKASI: Menangani 15 soal gabungan dan Fix Error SQL Value Too Long
     */
    public function publishToMobile(Request $request)
    {
        $request->validate([
            'class_id' => 'required', 
            'title'    => 'required|string|max:255', 
            'duration' => 'required|integer'
        ]);

        $classId = $request->class_id;
        // Ambil SEMUA draf (misal 15 soal dari 3 mapel berbeda)
        $drafts  = TryoutDraft::where('class_id', $classId)->get();
        
        if ($drafts->isEmpty()) {
            return back()->with('error', 'Tidak ada draf soal untuk dipublish di kelas ini.');
        }

        DB::beginTransaction();
        try {
            // A. Buat Header Paket Tryout Resmi di Laravel
            $tryout = Tryout::create([
                'class_id'         => $classId,
                'title'            => trim($request->title),
                'duration_minutes' => (int)$request->duration, 
                'status'           => 'published',
                'is_active'        => true
            ]);

            $questionsForGo = [];
            
            foreach ($drafts as $d) {
                // ✨ FIX: Pastikan Kunci Jawaban hanya 1 karakter (A/B/C/D/E)
                // Ini mencegah error "value too long for type character(1)"
                $cleanKey = substr(trim(strtoupper($d->correct_answer)), 0, 1);
                if (empty($cleanKey)) $cleanKey = 'A'; // Default jika kosong

                // B. Simpan ke database Laravel (Tabel Questions resmi untuk Mobile)
                Question::create([
                    'tryout_id'      => $tryout->tryout_id,
                    'class_id'       => $classId,
                    'subject'        => $d->subject_name, // Menyimpan "Biology", "Mathematics", dll
                    'question'       => $d->question,      // Teks pertanyaan asli
                    'option_a'       => $d->option_a,
                    'option_b'       => $d->option_b,
                    'option_c'       => $d->option_c,
                    'option_d'       => $d->option_d,
                    'option_e'       => $d->option_e,
                    'correct_answer' => $cleanKey, 
                    'explanation'    => $d->explanation,
                ]);

                // C. Siapkan data array untuk sinkronisasi ke Microservice Go (Port 9003)
                $questionsForGo[] = [
                    'tryout_id'      => (int)$tryout->tryout_id,
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

            // D. Sinkronisasi ke GO Service (Port 9003)
            $goUrl = env('GO_TRYOUT_URL', 'http://127.0.0.1:9003');
            Http::timeout(20)->post($goUrl . '/api/tryouts/sync', [
                'tryout' => [
                    'tryout_id' => (int)$tryout->tryout_id,
                    'class_id'  => (int)$classId,
                    'title'     => $tryout->title,
                    'duration'  => (int)$request->duration,
                    'is_active' => true
                ],
                'questions' => $questionsForGo
            ]);

            // E. Hapus draf soal yang baru saja dipublish agar Review bersih kembali
            TryoutDraft::where('class_id', $classId)->delete();

            DB::commit();
            return redirect()->route('admin.tryout.index')->with('success', 'Berhasil mempublish paket ('.count($questionsForGo).' soal) ke aplikasi mobile!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Publish Error: " . $e->getMessage());
            return back()->with('error', 'Gagal Publish: ' . $e->getMessage());
        }
    }

    /**
     * 5. REKAP NILAI SISWA
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

    public function lihatNilai($tryout_id) {
        $tryout = Tryout::where('tryout_id', $tryout_id)->first();
        if (!$tryout) {
            return redirect()->route('admin.scores.index')->with('error', 'Paket Tryout tidak ditemukan.');
        }

        $results = TryoutResult::where('tryout_id', $tryout_id)->latest()->get();
        foreach ($results as $res) {
            $res->user_data = User::where('usersID', $res->user_id)->first();
        }

        return view('admin.tryout.scores', compact('tryout', 'results'));
    }

    /**
     * 6. HAPUS DATA
     */
    public function deleteDraft($id) {
        TryoutDraft::destroy($id);
        return back()->with('success', 'Draf berhasil dihapus.');
    }

    public function destroyPackage($tryout_id) {
        DB::beginTransaction();
        try {
            Tryout::where('tryout_id', $tryout_id)->delete();
            Question::where('tryout_id', $tryout_id)->delete();
            DB::commit();
            return back()->with('success', 'Paket telah dihapus dari sistem.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus paket.');
        }
    }
}