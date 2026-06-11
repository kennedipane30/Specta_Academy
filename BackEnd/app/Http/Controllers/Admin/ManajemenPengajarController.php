<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TeacherAssignment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ManajemenPengajarController extends Controller
{
    /**
     * Ambil data mata pelajaran dari Microservice Materi
     */
    private function getSubjectsFromMicroservice(): array
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $response = Http::timeout(5)->get("$goUrl/api/materials");

            if ($response->successful()) {
                $materials = $response->json()['data'] ?? [];

                // Extract unique subject names dari materials
                $subjects = [];
                $seen = [];

                foreach ($materials as $material) {
                    $subjectName = $material['subject_name'] ?? $material['material_name'] ?? '';
                    if (!empty($subjectName) && !in_array($subjectName, $seen)) {
                        $seen[] = $subjectName;
                        $subjects[] = (object) [
                            'subject_id' => md5($subjectName),
                            'name' => $subjectName,
                        ];
                    }
                }

                // Sort by name
                usort($subjects, function($a, $b) {
                    return strcmp($a->name, $b->name);
                });

                return $subjects;
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil data subjects dari microservice: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Ambil distribusi bidang berdasarkan data dari microservice
     */
    private function getDistribusiBidangFromMicroservice(): array
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $response = Http::timeout(5)->get("$goUrl/api/materials");

            if ($response->successful()) {
                $materials = $response->json()['data'] ?? [];

                // Hitung distribusi berdasarkan subject_name
                $distribution = [];
                foreach ($materials as $material) {
                    $subjectName = $material['subject_name'] ?? $material['material_name'] ?? 'Unknown';
                    if (!isset($distribution[$subjectName])) {
                        $distribution[$subjectName] = 0;
                    }
                    $distribution[$subjectName]++;
                }

                // Convert ke array untuk view
                $result = [];
                foreach ($distribution as $name => $total) {
                    $result[] = (object) [
                        'subject_name' => $name,
                        'total' => $total,
                    ];
                }

                // Sort by total descending
                usort($result, function($a, $b) {
                    return $b->total <=> $a->total;
                });

                return $result;
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil distribusi bidang: " . $e->getMessage());
        }

        return [];
    }

    public function index(Request $request)
    {
        // ✅ MODIFIKASI: Hapus 'assignments.subject' karena tidak ada relasi lagi
        $teacherQuery = User::where('role_id', 2)
            ->with(['assignments.classModel']); // Hanya ambil classModel, bukan subject

        // Search filter
        if ($request->filled('search')) {
            $search = strtolower($request->search);

            $teacherQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"]);
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $teacherQuery->where('is_verified', true);
            }
            if ($request->status === 'inactive') {
                $teacherQuery->where('is_verified', false);
            }
        }

        $teachers = $teacherQuery
            ->latest('created_at')
            ->paginate(7)
            ->withQueryString();

        $teacherIds = $teachers->getCollection()->pluck('usersID');

        $assignmentMap = TeacherAssignment::with(['classModel'])
            ->whereIn('user_id', $teacherIds)
            ->get()
            ->groupBy('user_id');

        $scheduleCountMap = Schedule::whereIn('teacher_id', $teacherIds)
            ->whereDate('date', '>=', now()->toDateString())
            ->select('teacher_id', DB::raw('COUNT(DISTINCT class_id) as total'))
            ->groupBy('teacher_id')
            ->pluck('total', 'teacher_id');

        // ✅ AMBIL SUBJECTS DARI MICROSERVICE
        $subjects = $this->getSubjectsFromMicroservice();

        $totalPengajar = User::where('role_id', 2)->count();

        $pengajarAktif = User::where('role_id', 2)
            ->where('is_verified', true)
            ->count();

        $pengajarBaruBulanIni = User::where('role_id', 2)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $pengajarBulanLalu = User::where('role_id', 2)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();

        $growthPengajar = $this->growthPercent($pengajarBaruBulanIni, $pengajarBulanLalu);

        $kelasDiajar = TeacherAssignment::distinct()->count('class_id');

        // ✅ AMBIL DISTRIBUSI BIDANG DARI MICROSERVICE
        $distribusiBidang = $this->getDistribusiBidangFromMicroservice();
        $totalDistribusiBidang = collect($distribusiBidang)->sum('total');

        // Aktivitas Pengajar
        $aktivitasPengajar = User::where('role_id', 2)
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($teacher) {
                return [
                    'icon' => 'fa-user-plus',
                    'title' => $teacher->name,
                    'description' => 'Ditambahkan sebagai pengajar',
                    'time' => $teacher->created_at,
                ];
            });

        // Aktivitas Assignment (tanpa subject name dari DB)
        $aktivitasAssignment = TeacherAssignment::with(['teacher', 'classModel'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($assignment) {
                return [
                    'icon' => 'fa-book-open',
                    'title' => $assignment->teacher->name ?? 'Pengajar',
                    'description' => 'Ditugaskan mengajar kelas ' . ($assignment->classModel->program_name ?? ''),
                    'time' => $assignment->created_at,
                ];
            });

        $aktivitasJadwal = Schedule::with(['teacher', 'class'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($schedule) {
                return [
                    'icon' => 'fa-calendar-days',
                    'title' => $schedule->teacher->name ?? 'Pengajar',
                    'description' => 'Jadwal mengajar diperbarui',
                    'time' => $schedule->created_at,
                ];
            });

        $aktivitasTerbaru = collect()
            ->merge($aktivitasPengajar)
            ->merge($aktivitasAssignment)
            ->merge($aktivitasJadwal)
            ->sortByDesc('time')
            ->take(5)
            ->values();

        return view('admin.pengajar.index', compact(
            'teachers',
            'subjects',
            'assignmentMap',
            'scheduleCountMap',
            'totalPengajar',
            'pengajarAktif',
            'pengajarBaruBulanIni',
            'growthPengajar',
            'kelasDiajar',
            'distribusiBidang',
            'totalDistribusiBidang',
            'aktivitasTerbaru'
        ));
    }

    public function create()
    {
        return view('admin.pengajar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:30',
            'password'    => 'required|string|min:6',
            'is_verified' => 'required|in:0,1',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
            'role_id'     => 2,
            'is_verified' => (bool) $request->is_verified,
        ]);

        return redirect()
            ->route('admin.manajemen-pengajar.index')
            ->with('success', 'Akun pengajar berhasil didaftarkan!');
    }

    public function edit($id)
    {
        $teacher = User::where('role_id', 2)->findOrFail($id);

        return view('admin.pengajar.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $teacher = User::where('role_id', 2)->findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->usersID, 'usersID'),
            ],
            'phone'       => 'required|string|max:30',
            'password'    => 'nullable|string|min:6',
            'is_verified' => 'required|in:0,1',
        ]);

        $payload = [
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'is_verified' => (bool) $request->is_verified,
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->password);
        }

        $teacher->update($payload);

        return redirect()
            ->route('admin.manajemen-pengajar.index')
            ->with('success', 'Data pengajar berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $teacher = User::where('role_id', 2)->findOrFail($id);
        $teacher->delete();

        return redirect()
            ->route('admin.manajemen-pengajar.index')
            ->with('success', 'Akun pengajar berhasil dihapus!');
    }

    private function growthPercent($current, $previous)
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
