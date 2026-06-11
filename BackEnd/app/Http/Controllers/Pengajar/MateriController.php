<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    /**
     * ✅ LANGSUNG DARI TEACHER_ASSIGNMENTS
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
     * ✅ VALIDASI: Cek apakah pengajar memiliki penugasan
     */
    private function hasAssignment(int $classId, string $subjectName): bool
    {
        return TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $classId)
            ->where('subject_name', $subjectName)
            ->exists();
    }

    public function index()
    {
        $assignmentsWithSubjects = $this->getAssignmentsWithSubjects();
        return view('pengajar.materi.index', compact('assignmentsWithSubjects'));
    }

    public function pilihMateri($class_id, $subject_name)
{
    if (!$this->hasAssignment($class_id, $subject_name)) {
        abort(403, 'Anda tidak ditugaskan untuk kelas dan mata pelajaran ini.');
    }

    $class = ClassModel::findOrFail($class_id);

    $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
    Log::info('Pilih Materi - GO_URL: ' . $goUrl);

    $response = Http::timeout(5)->get("$goUrl/api/materials", [
        'class_id' => $class_id
    ]);

    Log::info('Pilih Materi - Response Status: ' . $response->status());
    Log::info('Pilih Materi - Response Body: ' . $response->body());

    $allMaterials = $response->json()['data'] ?? [];

    $materis = array_filter($allMaterials, function($item) use ($subject_name) {
        return ($item['subject_name'] ?? $item['material_name'] ?? '') == $subject_name;
    });

    usort($materis, function($a, $b) {
        return ($a['week'] ?? 0) <=> ($b['week'] ?? 0);
    });

    $materis = collect($materis);

    return view('pengajar.materi.pilih', compact('class', 'materis', 'subject_name'));
}
    public function store(Request $request, $class_id)
{
    $request->validate([
        'title'         => 'required|string|max:255',
        'material_name' => 'required|string',
        'file_pdf'      => 'nullable|mimes:pdf|max:10240',
        'week'          => 'required|integer|min:1|max:20',
    ]);

    Log::info('=== STORE MATERI ===');
    Log::info('Class ID: ' . $class_id);
    Log::info('Material Name: ' . $request->material_name);
    Log::info('Week: ' . $request->week);
    Log::info('Title: ' . $request->title);

    if (!$this->hasAssignment($class_id, $request->material_name)) {
        Log::warning('Tidak memiliki penugasan untuk: ' . $request->material_name);
        return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
    }

    $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
    Log::info('GO_MATERI_URL: ' . $goUrl);

    // Cek apakah materi sudah ada
    $response = Http::timeout(5)->get("$goUrl/api/materials", [
        'class_id' => $class_id
    ]);

    $existingMaterials = $response->json()['data'] ?? [];
    $existing = null;

    foreach ($existingMaterials as $m) {
        if (($m['subject_name'] ?? $m['material_name'] ?? '') == $request->material_name &&
            ($m['week'] ?? 0) == $request->week) {
            $existing = $m;
            Log::info('Materi sudah ada, akan diupdate. ID: ' . ($existing['material_id'] ?? 'null'));
            break;
        }
    }

    $filePath = null;
    if ($request->hasFile('file_pdf')) {
        $path = $request->file('file_pdf')->store('materi', 'public');
        $filePath = Storage::url($path);
        Log::info('File uploaded, path: ' . $filePath);
    } elseif (!$existing && !$request->hasFile('file_pdf')) {
        Log::warning('File PDF tidak diunggah untuk materi baru');
        return back()->with('error', 'File PDF wajib diunggah untuk materi baru.');
    }

    // ✅ Format payload sesuai microservice (UserID sebagai integer)
    $payload = [
        'class_id'      => (int) $class_id,
        'user_id'       => Auth::user()->usersID,
        'title'         => $request->title,
        'subject_name'  => $request->material_name,
        'week'          => (int) $request->week,
        'file_path'     => $filePath ?? '',
    ];

    Log::info('Payload yang dikirim ke microservice: ', $payload);

    try {
        if ($existing) {
            // UPDATE
            $payload['material_id'] = $existing['material_id'];
            $updateRes = Http::timeout(30)->put("$goUrl/api/materials/{$existing['material_id']}", $payload);

            Log::info('UPDATE Response Status: ' . $updateRes->status());
            Log::info('UPDATE Response Body: ' . $updateRes->body());

            if ($updateRes->successful()) {
                return back()->with('success', 'Materi Minggu ' . $request->week . ' berhasil diperbarui!');
            }
            return back()->with('error', 'Gagal memperbarui materi: ' . $updateRes->body());
        } else {
            // CREATE
            $createRes = Http::timeout(30)->post("$goUrl/api/materials", $payload);

            Log::info('CREATE Response Status: ' . $createRes->status());
            Log::info('CREATE Response Body: ' . $createRes->body());

            if ($createRes->successful()) {
                return back()->with('success', 'Materi baru berhasil ditambahkan!');
            }
            return back()->with('error', 'Gagal menyimpan materi: ' . $createRes->body());
        }

    } catch (\Exception $e) {
        Log::error("Sync Error: " . $e->getMessage());
        return back()->with('error', 'Gagal terhubung ke Microservice Materi: ' . $e->getMessage());
    }
}

    public function destroy($id)
    {
        $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');

        try {
            $response = Http::timeout(5)->get("$goUrl/api/materials/$id");

            if ($response->successful()) {
                $material = $response->json();
                if (($material['user_id'] ?? 0) != Auth::user()->usersID) {
                    return back()->with('error', 'Anda tidak memiliki izin untuk menghapus materi ini.');
                }
            }

            $deleteRes = Http::timeout(10)->delete("$goUrl/api/materials/$id");

            if ($deleteRes->successful()) {
                return back()->with('success', 'Materi telah dihapus.');
            }

            return back()->with('error', 'Gagal menghapus materi.');

        } catch (\Exception $e) {
            Log::error("Delete Error: " . $e->getMessage());
            return back()->with('error', 'Gagal terhubung ke Microservice Materi.');
        }
    }
}
