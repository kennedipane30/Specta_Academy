<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ManajemenSiswaController extends Controller
{
    /**
     * Ambil nilai rata-rata tryout dari microservice
     */
    private function getAverageScoresFromMicroservice(array $userIds): array
    {
        $scores = [];

        try {
            $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');

            foreach ($userIds as $userId) {
                $response = Http::timeout(3)->get("$goUrl/api/tryouts/history", [
                    'user_id' => $userId
                ]);

                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    if (!empty($data)) {
                        $totalScore = 0;
                        $count = 0;
                        foreach ($data as $result) {
                            if (isset($result['score'])) {
                                $totalScore += (float) $result['score'];
                                $count++;
                            }
                        }
                        if ($count > 0) {
                            $scores[$userId] = round($totalScore / $count, 1);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil nilai tryout dari microservice: " . $e->getMessage());
        }

        return $scores;
    }

    public function index(Request $request)
    {
        // Ambil data user role siswa (3)
        $query = User::where('role_id', 3)
            ->with(['student.class']);

        // 1. FILTER PENCARIAN (Nama, Email, NIS)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('national_id_number', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // 2. FILTER BERDASARKAN KELAS
        if ($request->filled('class_id')) {
            $classId = $request->class_id;
            $query->whereHas('classes', function ($q) use ($classId) {
                $q->where('enrollments.class_id', $classId)
                  ->where('enrollments.status', 'active');
            });
        }

        // 3. FILTER BERDASARKAN STATUS (Active/Expired)
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('classes', function ($classQuery) use ($status) {
                $classQuery->where('enrollments.status', $status);
            });
        }

        // Eksekusi Query dengan Pagination
        $siswas = $query->latest('created_at')->paginate(8)->withQueryString();
        $studentUserIds = $siswas->getCollection()->pluck('usersID')->toArray();

        // MAP UNTUK MENAMPILKAN KELAS & PROGRAM DI TABEL
        $latestEnrollmentMap = Enrollment::with('class')
            ->whereIn('user_id', $studentUserIds)
            ->where('status', 'active')
            ->latest('created_at')
            ->get()
            ->unique('user_id')
            ->keyBy('user_id');

        // ✅ AMBIL NILAI RATA-RATA DARI MICROSERVICE (bukan dari database lokal)
        $avgScoreMap = $this->getAverageScoresFromMicroservice($studentUserIds);

        /*
        |--------------------------------------------------------------------------
        | STATISTIK KARTU (CARD)
        |--------------------------------------------------------------------------
        */
        $totalSiswa = User::where('role_id', 3)->count();

        $siswaAktif = Enrollment::where('status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $siswaBaruBulanIni = User::where('role_id', 3)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $siswaBulanLalu = User::where('role_id', 3)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();

        $kelasAktif = Enrollment::where('status', 'active')
            ->distinct('class_id')
            ->count('class_id');

        $pendingEnrollment = Enrollment::where('status', 'pending')->count();
        $growthSiswa = $this->growthPercent($siswaBaruBulanIni, $siswaBulanLalu);

        $classes = ClassModel::orderBy('program_name')->get();

        /*
        |--------------------------------------------------------------------------
        | DISTRIBUSI PROGRAM (CHART)
        |--------------------------------------------------------------------------
        */
        $distribusiProgram = ClassModel::leftJoin('enrollments', function($join) {
                $join->on('classes.class_id', '=', 'enrollments.class_id')
                     ->where('enrollments.status', '=', 'active');
            })
            ->select(
                'classes.class_id',
                'classes.program_name',
                DB::raw('COUNT(enrollments.enrollment_id) as total')
            )
            ->groupBy('classes.class_id', 'classes.program_name')
            ->orderBy('classes.program_name')
            ->get();

        $totalDistribusi = $distribusiProgram->sum('total');

        /*
        |--------------------------------------------------------------------------
        | AKTIVITAS TERBARU
        |--------------------------------------------------------------------------
        */
        $aktivitasSiswa = User::where('role_id', 3)->latest()->take(4)->get()->map(function ($user) {
            return [
                'icon' => 'fa-user-plus',
                'title' => $user->name,
                'description' => 'Mendaftar sebagai siswa baru',
                'time' => $user->created_at,
                'type' => 'student',
            ];
        });

        $aktivitasEnrollment = Enrollment::with(['user', 'class'])->latest()->take(4)->get()->map(function ($enrollment) {
            return [
                'icon' => $enrollment->status === 'pending' ? 'fa-clock' : 'fa-circle-check',
                'title' => $enrollment->user->name ?? 'Siswa',
                'description' => ($enrollment->class->program_name ?? 'Program kelas') . ' - ' . ucfirst($enrollment->status),
                'time' => $enrollment->created_at,
                'type' => $enrollment->status,
            ];
        });

        $aktivitasTerbaru = collect()->merge($aktivitasSiswa)->merge($aktivitasEnrollment)->sortByDesc('time')->take(5)->values();

        return view('admin.siswa.index', compact(
            'siswas', 'classes', 'latestEnrollmentMap', 'avgScoreMap', 'totalSiswa', 'siswaAktif',
            'siswaBaruBulanIni', 'kelasAktif', 'pendingEnrollment', 'growthSiswa', 'distribusiProgram',
            'totalDistribusi', 'aktivitasTerbaru'
        ));
    }

    public function indexPendaftaran()
    {
        $data = Enrollment::with(['user.student', 'class'])
            ->where('status', 'pending')
            ->latest()
            ->get();
        $totalPending = $data->count();
        return view('admin.siswa.pendaftaran', compact('data', 'totalPending'));
    }

    public function formAktivasi($id)
    {
        $enroll = Enrollment::with(['user.student', 'class'])->findOrFail($id);
        return view('admin.siswa.aktivasi_form', compact('enroll'));
    }

    public function prosesAktivasi(Request $request, $id)
    {
        $request->validate(['durasi' => 'required|numeric|min:1']);
        $enroll = Enrollment::findOrFail($id);
        $enroll->update([
            'status' => 'active',
            'expires_at' => now()->addDays((int) $request->durasi)
        ]);

        // SINKRONISASI: Agar data tabel students tidak kosong lagi
        Student::where('user_id', $enroll->user_id)->update(['class_id' => $enroll->class_id]);

        return redirect()->route('admin.siswa.pendaftaran')->with('success', 'Siswa berhasil diaktifkan!');
    }

    private function growthPercent($current, $previous): float|int
    {
        if ($previous <= 0) { return $current > 0 ? 100 : 0; }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
