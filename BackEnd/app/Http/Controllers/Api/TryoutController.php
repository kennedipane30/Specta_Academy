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
    private function goUrl(): string
    {
        return env('GO_TRYOUT_URL', 'http://127.0.0.1:9003');
    }

    private function getClassId(Request $request): ?int
    {
        $user = $request->user();
        $userId = $user->usersID ?? $user->id;

        $enrollment = DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if ($enrollment) return (int) $enrollment->class_id;

        return $user->student?->class_id ? (int) $user->student->class_id : null;
    }

    /**
     * ✨ FIX: INDEX TRYOUT
     * Memastikan data dari Go tidak terbungkus dua kali (Double Wrapping)
     */
    public function index(Request $request)
    {
        try {
            $classId = $this->getClassId($request);
            
            $response = Http::get($this->goUrl() . '/api/tryouts', [
                'class_id' => $classId
            ]);

            if ($response->successful()) {
                $goData = $response->json();
                // Kirim hanya array datanya saja ke Flutter
                return response()->json([
                    'status' => 'success',
                    'data'   => $goData['data'] ?? []
                ]);
            }

            return response()->json(['status' => 'error', 'data' => []], $response->status());
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Service Tryout Offline'], 500);
        }
    }

    /**
     * ✨ FIX: QUESTIONS
     * Mengambil soal dari Go dan mengirimkan list flat ke Flutter
     */
    public function questions(Request $request, $id)
    {
        try {
            $response = Http::get($this->goUrl() . '/api/questions', [
                'tryout_id' => (int) $id
            ]);

            if ($response->successful()) {
                $goData = $response->json();
                return response()->json([
                    'status' => 'success',
                    'data'   => $goData['data'] ?? []
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Gagal memuat soal'], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Service Tryout Offline'], 500);
        }
    }

    public function history(Request $request)
    {
        $results = TryoutResult::where('user_id', $request->user()->usersID)
                    ->latest()
                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    public function submit(Request $request, $id)
{
    $user = $request->user();
    $userAnswers = $request->input('answers'); // Format: {"1":"A", "2":"B"}

    try {
        // 1. Ambil soal dari GO (Port 9003) untuk validasi jawaban
        $response = Http::get($this->goUrl() . '/api/questions', [
            'tryout_id' => $id
        ]);
        
        $questions = $response->json()['data'] ?? [];
        $correct = 0;

        foreach ($questions as $q) {
            $qId = $q['question_id'];
            if (isset($userAnswers[$qId])) {
                // Bandingkan jawaban user dengan kunci di database GO
                if (strtoupper($userAnswers[$qId]) == strtoupper($q['correct_answer'])) {
                    $correct++;
                }
            }
        }

        // 2. Hitung Skor
        $totalSoal = count($questions);
        $score = ($totalSoal > 0) ? round(($correct / $totalSoal) * 100) : 0;

        // 3. ✨ SIMPAN KE DATABASE ADMIN (Laravel)
        // Gunakan (int) untuk memastikan tipe data benar
        $result = \App\Models\TryoutResult::create([
            'user_id'       => (int) $user->usersID, // 🔥 PENTING: Gunakan usersID
            'tryout_id'     => (int) $id,
            'score'         => (int) $score,
            'total_correct' => (int) $correct,
        ]);

        // 4. KIRIM RIWAYAT KE GO (Database specta_tryout)
        Http::post($this->goUrl() . '/api/tryouts/submissions/sync', [
            'user_id'      => (int) $user->usersID,
            'tryout_id'    => (int) $id,
            'answers'      => json_encode($userAnswers),
            'score'        => (float) $score,
            'submitted_at' => now()->toDateTimeString()
        ]);

        return response()->json([
            'success' => true,
            'score'   => $score,
            'correct' => $correct,
            'total'   => $totalSoal
        ]);

    } catch (\Exception $e) {
        \Log::error("Gagal simpan nilai Admin: " . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}