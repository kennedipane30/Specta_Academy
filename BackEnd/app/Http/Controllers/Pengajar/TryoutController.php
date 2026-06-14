<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\TeacherAssignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TryoutController extends Controller
{
    /**
     * ✅ LANGSUNG DARI TEACHER_ASSIGNMENTS
     */
    private function getAssignmentsWithSubjects(): array
    {
        $userId = Auth::user()->usersID;
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        $assignments = TeacherAssignment::with(['classModel'])
            ->where('user_id', $userId)
            ->get();

        $result = [];
        foreach ($assignments as $assignment) {
            // Ambil jumlah draft dari microservice
            $totalSoal = 0;
            try {
                $response = Http::timeout(5)->get($goUrl . '/api/tryouts/drafts/count', [
                    'class_id' => $assignment->class_id,
                    'user_id' => $userId,
                    'subject_name' => $assignment->subject_name
                ]);

                if ($response->successful()) {
                    $totalSoal = $response->json()['count'] ?? 0;
                }
            } catch (\Exception $e) {
                Log::warning('Gagal mengambil jumlah draft: ' . $e->getMessage());
            }

            $result[] = (object) [
                'class_id'     => $assignment->class_id,
                'classModel'   => $assignment->classModel,
                'subject_name' => $assignment->subject_name,
                'total_soal'   => $totalSoal,
            ];
        }

        return $result;
    }

    /**
     * ✅ VALIDASI: Cek apakah pengajar memiliki penugasan
     */
    private function hasAssignment(int $classId, string $subjectName): bool
    {
        return TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $classId)
            ->where('subject_name', $subjectName)
            ->exists();
    }

    /**
     * 1. DASHBOARD TRYOUT PENGAJAR
     */
    public function index()
    {
        $assignmentsWithSubjects = $this->getAssignmentsWithSubjects();

        // Hitung total soal dari microservice
        $totalSoalSelesai = 0;
        $userId = Auth::user()->usersID;
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        try {
            $response = Http::timeout(5)->get($goUrl . '/api/tryouts/drafts/count', [
                'user_id' => $userId
            ]);

            if ($response->successful()) {
                $totalSoalSelesai = $response->json()['count'] ?? 0;
            }
        } catch (\Exception $e) {
            Log::warning('Gagal mengambil total draft: ' . $e->getMessage());
        }

        return view('pengajar.tryout.index', compact('assignmentsWithSubjects', 'totalSoalSelesai'));
    }

    /**
     * 2. FORM CREATE/EDIT SOAL
     */
    public function create($class_id, $subject_name)
    {
        if (!$this->hasAssignment($class_id, $subject_name)) {
            abort(403, 'Anda tidak ditugaskan untuk kelas dan mata pelajaran ini.');
        }

        $classModel = ClassModel::findOrFail($class_id);
        $userId = Auth::user()->usersID;
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        // Ambil draft soal dari microservice
        $existingSoal = [];
        try {
            $response = Http::timeout(5)->get($goUrl . '/api/tryouts/drafts', [
                'class_id' => $class_id,
                'user_id' => $userId,
                'subject_name' => $subject_name
            ]);

            if ($response->successful()) {
                $existingSoal = $response->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning('Gagal mengambil draft soal: ' . $e->getMessage());
        }

        return view('pengajar.tryout.create', [
            'classId'      => $class_id,
            'classModel'   => $classModel,
            'subjectName'  => trim($subject_name),
            'existingSoal' => $existingSoal
        ]);
    }

    /**
     * 3. SIMPAN SOAL (CREATE/UPDATE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'draft_id'       => 'nullable|integer',
            'class_id'       => 'required|integer',
            'subject_name'   => 'required|string',
            'question'       => 'required|string',
            'option_a'       => 'required|string',
            'option_b'       => 'required|string',
            'option_c'       => 'required|string',
            'option_d'       => 'required|string',
            'option_e'       => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D,E',
        ]);

        if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $userId = Auth::user()->usersID;

        $payload = [
            'class_id'       => (int) $request->class_id,
            'user_id'        => (int) $userId,
            'subject_name'   => trim($request->subject_name),
            'question'       => $request->question,
            'option_a'       => trim($request->option_a),
            'option_b'       => trim($request->option_b),
            'option_c'       => trim($request->option_c),
            'option_d'       => trim($request->option_d),
            'option_e'       => trim($request->option_e),
            'correct_answer' => strtoupper($request->correct_answer),
            'explanation'    => $request->explanation,
        ];

        try {
            if ($request->draft_id && $request->draft_id > 0) {
                // UPDATE draft
                $payload['id'] = (int) $request->draft_id;
                $response = Http::timeout(10)->put($goUrl . '/api/tryouts/drafts/' . $request->draft_id, $payload);
            } else {
                // CREATE draft baru
                $response = Http::timeout(10)->post($goUrl . '/api/tryouts/drafts', $payload);
            }

            if ($response->successful()) {
                return back()->with('success', 'Berhasil! Soal telah disimpan ke draf.');
            } else {
                $errorMsg = $response->json()['error'] ?? 'Unknown error';
                return back()->with('error', 'Gagal menyimpan: ' . $errorMsg);
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Koneksi ke Go service gagal: " . $e->getMessage());
            return back()->with('error', 'Server tryout sedang bermasalah. Silakan coba lagi nanti.');
        } catch (\Exception $e) {
            Log::error("Store Draft Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    /**
     * 4. IMPORT CSV
     */
    public function importCSV(Request $request)
    {
        $request->validate([
            'file_csv'     => 'required|mimes:csv,txt',
            'class_id'     => 'required|integer',
            'subject_name' => 'required|string'
        ]);

        if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), "r");

        // Skip header
        fgetcsv($handle, 2000, ";");

        $count = 0;
        $userId = Auth::user()->usersID;
        $forcedSubject = trim($request->subject_name);
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
        $errors = [];

        try {
            while (($row = fgetcsv($handle, 2000, ";")) !== FALSE) {
                if (!isset($row[1]) || empty(trim($row[1]))) continue;

                $payload = [
                    'class_id'       => (int) $request->class_id,
                    'user_id'        => (int) $userId,
                    'subject_name'   => $forcedSubject,
                    'question'       => trim($row[1]),
                    'option_a'       => trim($row[2] ?? '-'),
                    'option_b'       => trim($row[3] ?? '-'),
                    'option_c'       => trim($row[4] ?? '-'),
                    'option_d'       => trim($row[5] ?? '-'),
                    'option_e'       => trim($row[6] ?? '-'),
                    'correct_answer' => strtoupper(substr(trim($row[7] ?? 'A'), 0, 1)),
                    'explanation'    => trim($row[8] ?? ''),
                ];

                $response = Http::timeout(10)->post($goUrl . '/api/tryouts/drafts', $payload);

                if ($response->successful()) {
                    $count++;
                } else {
                    $errors[] = "Baris ke-" . ($count + 2) . " gagal: " . ($response->json()['error'] ?? 'Unknown');
                }
            }
            fclose($handle);

            if (count($errors) > 0) {
                return back()->with('warning', "Sukses! $count soal berhasil diimport. Namun " . count($errors) . " soal gagal.");
            }

            return back()->with('success', "Sukses! $count soal berhasil diimport ke mata pelajaran $forcedSubject.");

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            fclose($handle);
            Log::error("Koneksi ke Go service gagal: " . $e->getMessage());
            return back()->with('error', 'Server tryout sedang bermasalah. Silakan coba lagi nanti.');
        } catch (\Exception $e) {
            fclose($handle);
            Log::error("Import CSV Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * 5. HAPUS SATU DRAFT
     */
    public function destroy($id)
    {
        $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

        try {
            $response = Http::timeout(10)->delete($goUrl . '/api/tryouts/drafts/' . $id);

            if ($response->successful()) {
                return back()->with('success', 'Soal berhasil dihapus dari draf.');
            } else {
                return back()->with('error', 'Gagal menghapus soal.');
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Koneksi ke Go service gagal: " . $e->getMessage());
            return back()->with('error', 'Server tryout sedang bermasalah. Silakan coba lagi nanti.');
        } catch (\Exception $e) {
            Log::error("Delete Draft Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus soal.');
        }
    }

    /**
     * 6. HAPUS SEMUA DRAFT UNTUK SATU MAPEL
     */
   /**
 * 6. HAPUS SEMUA DRAFT UNTUK SATU MAPEL
 */
public function deleteAllDrafts(Request $request)
{
    $request->validate([
        'class_id'     => 'required|integer',
        'subject_name' => 'required|string'
    ]);

    if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
        return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
    }

    $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
    $userId = Auth::user()->usersID;

    try {
        // ✅ Kirim sebagai query string (bukan body array)
        $response = Http::timeout(10)->delete($goUrl . '/api/tryouts/drafts?' . http_build_query([
            'class_id' => $request->class_id,
            'user_id' => $userId,
            'subject_name' => $request->subject_name
        ]));

        if ($response->successful()) {
            return back()->with('success', "Seluruh draf untuk mata pelajaran ini telah dihapus.");
        } else {
            $errorMsg = $response->json()['error'] ?? 'Gagal membersihkan draf';
            return back()->with('error', $errorMsg);
        }

    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        Log::error("Koneksi ke Go service gagal: " . $e->getMessage());
        return back()->with('error', 'Server tryout sedang bermasalah. Silakan coba lagi nanti.');
    } catch (\Exception $e) {
        Log::error("Delete All Drafts Error: " . $e->getMessage());
        return back()->with('error', 'Gagal membersihkan draf: ' . $e->getMessage());
    }
}
}
