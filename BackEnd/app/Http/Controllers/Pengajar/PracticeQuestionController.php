<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PracticeQuestionController extends Controller
{
    /**
     * Get assignments with subjects for current teacher
     */
    private function getAssignmentsWithSubjects(): array
    {
        $userId = Auth::user()->usersID;

        $assignments = TeacherAssignment::with(['classModel'])
            ->where('user_id', $userId)
            ->get();

        $result = [];
        foreach ($assignments as $assignment) {
            $result[] = (object) [
                'class_id'     => $assignment->class_id,
                'classModel'   => $assignment->classModel,
                'subject_name' => $assignment->subject_name,
                'subject_id'   => $assignment->subject_id,
            ];
        }

        usort($result, function($a, $b) {
            $keyA = $a->class_id . '-' . $a->subject_name;
            $keyB = $b->class_id . '-' . $b->subject_name;
            return strcmp($keyA, $keyB);
        });

        return $result;
    }

    /**
     * Check if teacher has assignment for given class and subject
     */
    private function hasAssignment(int $classId, string $subjectName): bool
    {
        return TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $classId)
            ->where('subject_name', $subjectName)
            ->exists();
    }

    /**
     * Display list of practice assignments
     */
    public function index()
    {
        $assignmentsWithSubjects = $this->getAssignmentsWithSubjects();
        return view('pengajar.Latihan.index', compact('assignmentsWithSubjects'));
    }

    /**
     * Display practice management for specific class and subject
     */
    public function selectPractice($class_id, $subject_name)
    {
        if (!$this->hasAssignment($class_id, $subject_name)) {
            abort(403, 'Anda tidak ditugaskan untuk kelas dan mata pelajaran ini.');
        }

        $class = ClassModel::findOrFail($class_id);
        $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');

        // Fetch all practice questions
        $response = Http::timeout(5)->get("$goUrl/api/tryouts", [
            'class_id' => $class_id,
            '_' => time()
        ]);

        $allQuestions = [];
        if ($response->successful()) {
            $allQuestions = $response->json() ?? [];
        }

        // Filter by subject
        $filteredQuestions = array_filter($allQuestions, function($q) use ($subject_name) {
            return isset($q['subject']) && $q['subject'] == $subject_name;
        });

        // Group by week
        $practices = [];
        foreach ($filteredQuestions as $q) {
            $week = $q['week'];
            if (!isset($practices[$week])) {
                $practices[$week] = 0;
            }
            $practices[$week]++;
        }

        $practiceList = [];
        foreach ($practices as $week => $total) {
            $practiceList[] = (object) ['week' => $week, 'total_soal' => $total];
        }
        usort($practiceList, function($a, $b) {
            return $a->week <=> $b->week;
        });

        // AMBIL DRAF SOAL MANUAL
        $draftKey = "practice_drafts_{$class_id}_" . md5($subject_name);
        $drafts = session()->get($draftKey, []);
        $activeDraftWeek = count($drafts) > 0 ? $drafts[0]['week'] : null;

        return view('pengajar.Latihan.pilih', [
            'class'           => $class,
            'subject_name'    => $subject_name,
            'practices'       => $practiceList,
            'drafts'          => $drafts,
            'activeDraftWeek' => $activeDraftWeek,
            'draftKey'        => $draftKey
        ]);
    }

    /**
     * Import CSV file and sync to practice service
     */
    public function storeCSV(Request $request, $class_id)
    {
        $request->validate([
            'subject'  => 'required|string',
            'week'     => 'required|integer|min:1|max:20',
            'file_csv' => 'required|file|mimes:csv,txt'
        ]);

        if (!$this->hasAssignment($class_id, $request->subject)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");

        // Skip header row
        fgetcsv($handle, 2000, ";");

        $questionsForSync = [];
        $rowIndex = 0;

        while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
            if (!isset($row[0]) || empty(trim($row[0]))) continue;

            $questionsForSync[] = [
                'class_id'       => (int) $class_id,
                'subject'        => trim($request->subject),
                'week'           => (int) $request->week,
                'question'       => trim($row[0]),
                'option_a'       => trim($row[1] ?? '-'),
                'option_b'       => trim($row[2] ?? '-'),
                'option_c'       => trim($row[3] ?? '-'),
                'option_d'       => trim($row[4] ?? '-'),
                'correct_answer' => strtoupper(trim($row[5] ?? 'A')),
                'hint'           => $row[6] ?? null,
                'explanation'    => $row[7] ?? null,
            ];
            $rowIndex++;
        }
        fclose($handle);

        if (empty($questionsForSync)) {
            return back()->with('error', 'Tidak ada data yang valid di file CSV.');
        }

        try {
            $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
            $response = Http::timeout(30)->post($goUrl . '/api/practice/sync', $questionsForSync);

            if ($response->successful()) {
                return back()->with('success', "Berhasil! $rowIndex soal latihan minggu ke-{$request->week} telah disimpan.");
            } else {
                return back()->with('error', 'Gagal menyimpan ke microservice.');
            }

        } catch (\Exception $e) {
            Log::error("Koneksi ke Go Practice Service terputus: " . $e->getMessage());
            return back()->with('error', 'Gagal terhubung ke microservice Practice.');
        }
    }

    /**
     * Delete all questions for a specific week
     */
    public function destroyByWeek($class_id, $subject_name, $week)
    {
        // Verify authorization - menggunakan subject_name
        if (!$this->hasAssignment($class_id, $subject_name)) {
            Log::warning("Unauthorized delete attempt", [
                'class_id' => $class_id,
                'subject' => $subject_name,
                'week' => $week,
                'user' => Auth::user()->usersID ?? 'unknown'
            ]);
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        try {
            $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');

            // Build URL with query parameter for subject
            $deleteUrl = $goUrl . '/api/practice/class/' . $class_id . '/week/' . $week;
            $deleteUrlWithQuery = $deleteUrl . '?subject=' . urlencode($subject_name);

            $response = Http::timeout(10)->delete($deleteUrlWithQuery);

            if ($response->successful()) {
                return redirect()->route('pengajar.latihan.pilih', [
                    'class_id' => $class_id,
                    'subject_name' => $subject_name
                ])->with('success', "Semua soal minggu ke-$week berhasil dihapus!");
            } else {
                $errorBody = $response->body();
                return back()->with('error', 'Gagal menghapus: ' . $errorBody);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Display all questions for a specific week
     */
    public function showQuestions($class_id, $subject_name, $week)
    {
        if (!$this->hasAssignment($class_id, $subject_name)) {
            abort(403, 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
        $response = Http::timeout(5)->get("$goUrl/api/tryouts", [
            'class_id' => $class_id,
            '_' => time()
        ]);

        $questions = [];
        if ($response->successful()) {
            $allQuestions = $response->json() ?? [];
            $questions = array_filter($allQuestions, function($q) use ($subject_name, $week) {
                return isset($q['subject']) && $q['subject'] == $subject_name && $q['week'] == (int)$week;
            });
            $questions = array_values($questions);
        }

        $class = ClassModel::findOrFail($class_id);

        return view('pengajar.Latihan.questions', compact('class', 'subject_name', 'week', 'questions'));
    }

    // ============================================================
    // ✨ FUNGSI BARU UNTUK INPUT MANUAL (DRAFT)
    // ============================================================

    public function storeDraft(Request $request, $class_id)
    {
        // Validasi Draf. (Tidak ada validasi Option E agar sinkron dengan CSV)
        $request->validate([
            'subject'        => 'required|string',
            'week'           => 'required|integer',
            'question'       => 'required|string',
            'option_a'       => 'required|string',
            'option_b'       => 'required|string',
            'option_c'       => 'required|string',
            'option_d'       => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
            'hint'           => 'nullable|string',
            'explanation'    => 'nullable|string',
        ]);

        $draftKey = "practice_drafts_{$class_id}_" . md5($request->subject);
        $drafts = session()->get($draftKey, []);

        // Cegah pengajar mencampur soal minggu 1 dan minggu 2 dalam satu draf
        if (count($drafts) > 0 && $drafts[0]['week'] != $request->week) {
            return back()->with('error', 'Selesaikan atau hapus draf Minggu ke-' . $drafts[0]['week'] . ' terlebih dahulu!');
        }

        $drafts[] = [
            'id'             => uniqid(),
            'class_id'       => (int) $class_id,
            'subject'        => trim($request->subject),
            'week'           => (int) $request->week,
            'question'       => trim($request->question),
            'option_a'       => trim($request->option_a),
            'option_b'       => trim($request->option_b),
            'option_c'       => trim($request->option_c),
            'option_d'       => trim($request->option_d),
            'correct_answer' => strtoupper(trim($request->correct_answer)),
            'hint'           => trim($request->hint ?? ''), // Hint disimpan ke draf
            'explanation'    => trim($request->explanation ?? ''),
        ];

        session()->put($draftKey, $drafts);

        return back()->with('success', 'Soal berhasil ditambahkan ke Draf. (Belum Diterbitkan)');
    }

    public function deleteDraft(Request $request, $class_id, $draft_id)
    {
        $subject = $request->query('subject');
        $draftKey = "practice_drafts_{$class_id}_" . md5($subject);

        $drafts = session()->get($draftKey, []);
        $drafts = array_filter($drafts, function($d) use ($draft_id) {
            return $d['id'] !== $draft_id;
        });

        session()->put($draftKey, array_values($drafts));

        return back()->with('success', 'Soal dihapus dari Draf.');
    }

    public function publishDraft(Request $request, $class_id)
    {
        $draftKey = "practice_drafts_{$class_id}_" . md5($request->subject);
        $drafts = session()->get($draftKey, []);

        if (count($drafts) < 5) {
            return back()->with('error', 'Minimal harus membuat 5 soal sebelum menerbitkan latihan!');
        }

        // Susun ulang format agar persis seperti format upload CSV
        $questionsForSync = [];
        foreach ($drafts as $d) {
            unset($d['id']); // Hapus ID Draf yang hanya dibutuhkan di frontend Laravel
            $questionsForSync[] = $d;
        }

        try {
            $goUrl = env('GO_PRACTICE_URL', 'http://localhost:9003');
            // Kirim paket 5 soal ke Golang!
            $response = Http::timeout(30)->post($goUrl . '/api/practice/sync', $questionsForSync);

            if ($response->successful()) {
                session()->forget($draftKey); // Bersihkan list draf setelah sukses
                return back()->with('success', 'Berhasil! ' . count($questionsForSync) . ' soal latihan telah diterbitkan ke sistem.');
            } else {
                return back()->with('error', 'Gagal menerbitkan soal ke microservice.');
            }
        } catch (\Exception $e) {
            Log::error("Koneksi ke Go Practice Service terputus: " . $e->getMessage());
            return back()->with('error', 'Gagal terhubung ke microservice Practice.');
        }
    }
}
