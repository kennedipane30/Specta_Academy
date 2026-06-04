<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\TryoutResult;

class TryoutController extends Controller
{
    /**
     * Helper untuk URL Go Service
     */
    private function goUrl(): string
    {
        return env('GO_TRYOUT_URL', 'http://127.0.0.1:9003');
    }

    /**
     * OTOMATISASI KELAS: Mendapatkan Class ID Siswa secara dinamis.
     */
    private function getClassId(Request $request): ?int
    {
        $user = $request->user();
        if (!$user) return null;

        $userId = $user->usersID ?? $user->id;

        $enrollment = DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if ($enrollment) return (int) $enrollment->class_id;

        $studentClass = $user->student?->class_id;
        if ($studentClass) return (int) $studentClass;

        return null;
    }

    /**
     * 1. DAFTAR TRYOUT (Index)
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $classId = $this->getClassId($request);

            if ($classId === null) {
                return response()->json(['status' => 'success', 'data' => []]);
            }

            $response = Http::timeout(5)->get($this->goUrl() . '/api/tryouts', [
                'class_id' => $classId
            ]);

            if ($response->successful()) {
                $rawTryouts = $response->json()['data'] ?? [];
                if (!is_array($rawTryouts)) return response()->json(['status' => 'success', 'data' => []]);

                // Ambil daftar tryout_id yang sudah dikerjakan
                $completedIds = TryoutResult::where('user_id', $user->usersID ?? $user->id)
                    ->pluck('tryout_id')
                    ->toArray();

                $finalData = array_map(function($to) use ($completedIds) {
                    $currentId = $to['id'] ?? $to['tryout_id'] ?? null;
                    if ($currentId !== null) {
                        $to['id'] = $currentId;
                        $to['is_completed'] = in_array($currentId, $completedIds);
                    } else {
                        $to['is_completed'] = false;
                    }
                    return $to;
                }, $rawTryouts);

                return response()->json(['status' => 'success', 'data' => $finalData]);
            }
            return response()->json(['status' => 'error', 'message' => 'Gagal sinkronisasi data'], 502);
        } catch (\Exception $e) {
            Log::error("Index Tryout API Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    /**
     * 2. AMBIL SOAL (Questions)
     */
    public function questions(Request $request, $id)
    {
        try {
            $user = $request->user();
            $userId = $user->usersID ?? $user->id;

            // ✨ PROTEKSI: Gunakan tryout_id untuk mengecek apakah sudah dikerjakan
            $alreadyDone = TryoutResult::where('user_id', $userId)->where('tryout_id', $id)->exists();

            if ($alreadyDone) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah mengerjakan Tryout ini.'], 403); 
            }

            $response = Http::timeout(5)->get($this->goUrl() . '/api/questions', ['tryout_id' => (int) $id]);
            if ($response->successful()) {
                return response()->json(['status' => 'success', 'data' => $response->json()['data'] ?? []]);
            }
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat soal'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Service Offline'], 500);
        }
    }

    /**
     * 3. SUBMIT JAWABAN
     */
    public function submit(Request $request, $id)
    {
        $user = $request->user();
        $userId = $user->usersID ?? $user->id;

        $alreadySubmitted = TryoutResult::where('user_id', $userId)->where('tryout_id', $id)->exists();
        if ($alreadySubmitted) {
            return response()->json(['success' => false, 'message' => 'Jawaban sudah terkirim sebelumnya.'], 400);
        }

        $userAnswers = $request->input('answers') ?? []; 

        try {
            $response = Http::get($this->goUrl() . '/api/questions', ['tryout_id' => $id]);
            $questions = $response->json()['data'] ?? [];
            $correct = 0;

            foreach ($questions as $q) {
                $qId = (string) ($q['id'] ?? $q['question_id'] ?? '');
                if (isset($userAnswers[$qId])) {
                    if (strtoupper(trim($userAnswers[$qId])) == strtoupper(trim($q['correct_answer']))) {
                        $correct++;
                    }
                }
            }

            $totalSoal = count($questions);
            $score = ($totalSoal > 0) ? round(($correct / $totalSoal) * 100) : 0;

            // ✨ SIMPAN: Laravel otomatis mengisi result_id jika primary key diset di model
            $result = TryoutResult::create([
                'user_id'       => (int) $userId,
                'tryout_id'     => (int) $id,
                'score'         => (int) $score,
                'total_correct' => (int) $correct,
            ]);

            Http::post($this->goUrl() . '/api/tryouts/submissions/sync', [
                'user_id'      => (int) $userId,
                'tryout_id'    => (int) $id,
                'answers'      => json_encode($userAnswers), 
                'score'        => (float) $score,
                'submitted_at' => now()->toDateTimeString()
            ]);

            return response()->json(['success' => true, 'score' => $score, 'submission_id' => $result->result_id]);
        } catch (\Exception $e) {
            Log::error("Submit Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memproses jawaban'], 500);
        }
    }

    /**
     * 4. DAFTAR RIWAYAT
     */
    public function history(Request $request)
    {
        $userId = $request->user()->usersID ?? $request->user()->id;
        $results = TryoutResult::where('user_id', $userId)->latest()->get();
        
        // ✨ Pastikan mengirimkan result_id agar Flutter bisa memakainya
        return response()->json(['status' => 'success', 'data' => $results]);
    }

    /**
     * 5. HASIL PEMBAHASAN (Results)
     * ✨ FIX TERBESAR: Mengganti 'id' menjadi 'result_id' sesuai database PostgreSQL Anda.
     */
    public function results(Request $request, $id)
    {
        $user = $request->user();
        $userId = $user->usersID ?? $user->id;

        try {
            // ✨ FIX: Cari berdasarkan result_id, bukan id
            $resultRecord = TryoutResult::where('result_id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$resultRecord) {
                return response()->json(['status' => 'error', 'message' => 'Riwayat pengerjaan tidak ditemukan.'], 404);
            }

            // 2. Ambil Soal dari Go Service
            $qResponse = Http::timeout(5)->get($this->goUrl() . '/api/questions', ['tryout_id' => $resultRecord->tryout_id]);
            if (!$qResponse->successful()) {
                throw new \Exception("Gagal mengambil data soal dari Go Service.");
            }
            $questions = $qResponse->json()['data'] ?? [];

            // 3. Ambil Detail Jawaban User
            $sResponse = Http::timeout(5)->get($this->goUrl() . '/api/tryouts/submissions/detail', [
                'user_id'   => (int) $userId,
                'tryout_id' => (int) $resultRecord->tryout_id
            ]);

            $userAnswers = [];
            if ($sResponse->successful()) {
                $submissionData = $sResponse->json()['data'] ?? null;
                if ($submissionData && isset($submissionData['answers'])) {
                    $userAnswers = is_array($submissionData['answers']) 
                                   ? $submissionData['answers'] 
                                   : json_decode($submissionData['answers'], true);
                }
            }

            // 4. Gabungkan Data (Mapping)
            $finalData = array_map(function($q) use ($userAnswers) {
                $qId = (string) ($q['id'] ?? $q['question_id'] ?? '');
                $q['user_answer'] = $userAnswers[$qId] ?? '-'; 
                return $q;
            }, $questions);

            return response()->json([
                'status' => 'success',
                'data'   => $finalData
            ]);

        } catch (\Exception $e) {
            Log::error("DETAIL PEMBAHASAN ERROR [ResultID $id]: " . $e->getMessage());
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal memuat pembahasan: ' . $e->getMessage()
            ], 500);
        }
    }
}