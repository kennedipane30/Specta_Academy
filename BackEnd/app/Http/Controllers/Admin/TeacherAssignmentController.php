<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeacherAssignmentController extends Controller
{
    /**
     * Ambil data mata pelajaran dari Microservice Materi
     */
    private function getSubjectsFromMicroservice(int $classId = null): array
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $url = $goUrl . '/api/materials';

            if ($classId) {
                $url .= '?class_id=' . $classId;
            }

            $response = Http::timeout(5)->get($url);

            if ($response->successful()) {
                $materials = $response->json()['data'] ?? [];

                // Transform ke format yang sesuai dengan view
                $subjects = [];
                foreach ($materials as $material) {
                    $subjectName = $material['subject_name'] ?? $material['material_name'] ?? '';
                    $subjectId = $material['material_id'] ?? md5($subjectName);

                    // Hindari duplikasi berdasarkan nama
                    $existing = array_filter($subjects, function($s) use ($subjectName) {
                        return $s->material_name == $subjectName;
                    });

                    if (empty($existing) && !empty($subjectName)) {
                        $subjects[] = (object) [
                            'material_id' => $subjectId,
                            'material_name' => $subjectName,
                            'class_id' => $material['class_id'] ?? null,
                        ];
                    }
                }

                // Sort by name
                usort($subjects, function($a, $b) {
                    return strcmp($a->material_name, $b->material_name);
                });

                return $subjects;
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil data subjects dari microservice: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Ambil data mata pelajaran per kelas dari Microservice Materi
     */
    private function getSubjectsByClassFromMicroservice(int $classId): array
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $response = Http::timeout(5)->get($goUrl . '/api/materials', [
                'class_id' => $classId
            ]);

            if ($response->successful()) {
                $materials = $response->json()['data'] ?? [];

                $subjects = [];
                foreach ($materials as $material) {
                    $subjectName = $material['subject_name'] ?? $material['material_name'] ?? '';
                    if (!empty($subjectName)) {
                        $subjects[] = (object) [
                            'material_id' => $material['material_id'] ?? md5($subjectName),
                            'material_name' => $subjectName,
                        ];
                    }
                }

                // Remove duplicates based on name
                $uniqueSubjects = [];
                $seen = [];
                foreach ($subjects as $subject) {
                    if (!in_array($subject->material_name, $seen)) {
                        $seen[] = $subject->material_name;
                        $uniqueSubjects[] = $subject;
                    }
                }

                usort($uniqueSubjects, function($a, $b) {
                    return strcmp($a->material_name, $b->material_name);
                });

                return $uniqueSubjects;
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil subjects by class: " . $e->getMessage());
        }

        return [];
    }

    public function index()
    {
        $teachers = User::where('role_id', 2)->orderBy('name')->get();
        $classes = ClassModel::orderBy('program_name')->get();

        // ✅ AMBIL SUBJECTS DARI MICROSERVICE
        $subjects = $this->getSubjectsFromMicroservice();

        // Ambil data penugasan
        $assignments = TeacherAssignment::with(['classModel', 'teacher'])->get();

        return view('admin.assignments.index', compact('teachers', 'classes', 'assignments', 'subjects'));
    }

    /**
     * Get subjects by class (AJAX) - Data dari Microservice
     */
    public function getSubjectsByClass($class_id)
    {
        $subjects = $this->getSubjectsByClassFromMicroservice((int) $class_id);
        return response()->json($subjects);
    }

    /**
     * ✅ MODIFIKASI: Simpan juga subject_name
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_id'    => 'required|exists:users,usersID',
            'class_id'      => 'required|exists:classes,class_id',
            'subject_id'    => 'required|string',
            'subject_name'  => 'required|string'  // ✅ TAMBAHKAN validasi
        ]);

        // Cek apakah sudah ada penugasan untuk class dan subject yang sama
        $exists = TeacherAssignment::where([
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Mata pelajaran ini sudah memiliki pengajar di kelas tersebut.');
        }

        TeacherAssignment::create([
            'user_id'      => $request->teacher_id,
            'class_id'     => $request->class_id,
            'subject_id'   => $request->subject_id,
            'subject_name' => $request->subject_name  // ✅ TAMBAHKAN
        ]);

        return back()->with('success', 'Pengajar berhasil ditugaskan!');
    }

    public function destroy($id)
    {
        TeacherAssignment::findOrFail($id)->delete();
        return back()->with('success', 'Penugasan berhasil dihapus.');
    }
}
