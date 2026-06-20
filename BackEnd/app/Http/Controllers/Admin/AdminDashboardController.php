<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\DedicatedTutor;
use App\Models\Enrollment;
use App\Models\Promotion;
use App\Models\Banner;
use App\Models\Announcement;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Ambil parameter month dan year dari request
        $month = $request->query('month', Carbon::now()->month);
        $year = $request->query('year', Carbon::now()->year);

        // Pastikan month dan year valid
        $month = max(1, min(12, (int)$month));
        $year = max(2000, min(2100, (int)$year));

        // Ambil data dari microservice (dengan fallback jika offline)
        $total_materi_aktif = $this->getTotalMateriFromMicroservice();
        $total_tryout_aktif = $this->getTotalTryoutFromMicroservice();

        /*
        |--------------------------------------------------------------------------
        | 1. STATISTIK UTAMA
        |--------------------------------------------------------------------------
        */

        $total_siswa = User::where('role_id', 3)->count();
        $total_pengajar = User::where('role_id', 2)->count();
        $total_kelas_aktif = Enrollment::where('status', 'active')->count();
        $tutor_pending = DedicatedTutor::where('status', 'pending')->count();
        $pendaftaran_pending = Enrollment::where('status', 'pending')->count();

        /*
        |--------------------------------------------------------------------------
        | 2. PERTUMBUHAN BULANAN UNTUK BADGE CARD
        |--------------------------------------------------------------------------
        */

        $siswa_bulan_ini = User::where('role_id', 3)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $siswa_bulan_lalu = User::where('role_id', 3)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();

        $pengajar_bulan_ini = User::where('role_id', 2)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $pengajar_bulan_lalu = User::where('role_id', 2)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();

        $growthPercent = function ($current, $previous) {
            if ($previous <= 0) {
                return $current > 0 ? 100 : 0;
            }
            return round((($current - $previous) / $previous) * 100, 1);
        };

        $persen_siswa = $growthPercent($siswa_bulan_ini, $siswa_bulan_lalu);
        $persen_pengajar = $growthPercent($pengajar_bulan_ini, $pengajar_bulan_lalu);

        /*
        |--------------------------------------------------------------------------
        | 3. DATA GRAFIK PER BULAN DENGAN 4 MINGGU (DINAMIS BERDASARKAN PARAMETER)
        |--------------------------------------------------------------------------
        */

        // Gunakan parameter month dan year yang sudah didapat
        $currentMonth = $month;
        $currentYear = $year;

        // Tentukan minggu ke-1 sampai ke-4 dalam bulan yang dipilih
        $weeks = [];

        // Dapatkan hari pertama bulan yang dipilih
        $firstDayOfMonth = Carbon::create($currentYear, $currentMonth, 1);
        // Dapatkan hari terakhir bulan yang dipilih
        $lastDayOfMonth = Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();

        // Hitung minggu ke-1 (hari 1-7)
        $week1Start = $firstDayOfMonth->copy();
        $week1End = $firstDayOfMonth->copy()->addDays(6);

        // Minggu ke-2 (hari 8-14)
        $week2Start = $firstDayOfMonth->copy()->addDays(7);
        $week2End = $firstDayOfMonth->copy()->addDays(13);

        // Minggu ke-3 (hari 15-21)
        $week3Start = $firstDayOfMonth->copy()->addDays(14);
        $week3End = $firstDayOfMonth->copy()->addDays(20);

        // Minggu ke-4 (hari 22 - akhir bulan)
        $week4Start = $firstDayOfMonth->copy()->addDays(21);
        $week4End = $lastDayOfMonth->copy();

        $weeks = [
            [
                'start' => $week1Start,
                'end' => $week1End,
                'label' => 'Minggu 1',
                'range' => $week1Start->format('d') . '-' . $week1End->format('d M')
            ],
            [
                'start' => $week2Start,
                'end' => $week2End,
                'label' => 'Minggu 2',
                'range' => $week2Start->format('d') . '-' . $week2End->format('d M')
            ],
            [
                'start' => $week3Start,
                'end' => $week3End,
                'label' => 'Minggu 3',
                'range' => $week3Start->format('d') . '-' . $week3End->format('d M')
            ],
            [
                'start' => $week4Start,
                'end' => $week4End,
                'label' => 'Minggu 4',
                'range' => $week4Start->format('d') . '-' . $week4End->format('d M')
            ],
        ];

        $chart_labels = [];
        $chart_siswa_baru = [];
        $chart_aktivitas_harian = [];
        $chart_total_siswa = [];

        // Akumulasi total siswa dari bulan-bulan SEBELUM bulan yang dipilih
        $baseStudentCount = User::where('role_id', 3)
            ->where(function($query) use ($currentYear, $currentMonth) {
                $query->whereYear('created_at', '<', $currentYear)
                    ->orWhere(function($q) use ($currentYear, $currentMonth) {
                        $q->whereYear('created_at', $currentYear)
                            ->whereMonth('created_at', '<', $currentMonth);
                    });
            })
            ->count();

        $runningTotalStudent = $baseStudentCount;

        foreach ($weeks as $week) {
            $startDate = $week['start'];
            $endDate = $week['end'];

            // Hitung siswa baru per minggu (hanya dari bulan yang dipilih)
            $newStudents = User::where('role_id', 3)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();

            // Hitung aktivitas per minggu (enrollment + dedicated tutor)
            $enrollments = Enrollment::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();

            $tutors = DedicatedTutor::whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->count();

            $dailyActivity = $enrollments + $tutors;

            // Akumulasi total siswa
            $runningTotalStudent += $newStudents;

            $chart_labels[] = $week['label'] . "\n" . $week['range'];
            $chart_siswa_baru[] = $newStudents;
            $chart_aktivitas_harian[] = $dailyActivity;
            $chart_total_siswa[] = $runningTotalStudent;
        }

        /*
        |--------------------------------------------------------------------------
        | 4. DISTRIBUSI AKTIVITAS
        |--------------------------------------------------------------------------
        */

        $total_tutor_confirmed = DedicatedTutor::where('status', 'confirmed')->count();

        $distribusi_aktivitas = [
            [
                'label' => 'Kelas Aktif',
                'value' => $total_kelas_aktif,
            ],
            [
                'label' => 'Materi Tersedia',
                'value' => $total_materi_aktif,
            ],
            [
                'label' => 'Tryout Tersedia',
                'value' => $total_tryout_aktif,
            ],
            [
                'label' => 'Tutor Confirmed',
                'value' => $total_tutor_confirmed,
            ],
        ];

        $total_distribusi = collect($distribusi_aktivitas)->sum('value');

        /*
        |--------------------------------------------------------------------------
        | 5. TUGAS MENUNGGU
        |--------------------------------------------------------------------------
        */

        $pengajar_belum_verifikasi = User::where('role_id', 2)
            ->where('is_verified', false)
            ->count();

        $tugas_menunggu = [
            [
                'title' => 'Permintaan tutor baru',
                'subtitle' => 'Menunggu persetujuan',
                'count' => $tutor_pending,
                'icon' => 'fa-headset',
                'route' => route('admin.tutor.index'),
            ],
            [
                'title' => 'Konfirmasi kelas',
                'subtitle' => 'Menunggu verifikasi pembayaran/kelas',
                'count' => $pendaftaran_pending,
                'icon' => 'fa-calendar-check',
                'route' => route('admin.siswa.pendaftaran'),
            ],
            [
                'title' => 'Pengajar menunggu aktivasi',
                'subtitle' => 'Akun pengajar belum diverifikasi',
                'count' => $pengajar_belum_verifikasi,
                'icon' => 'fa-user-clock',
                'route' => route('admin.manajemen-pengajar.index'),
            ],
        ];

        $total_tugas_menunggu = collect($tugas_menunggu)->sum('count');

        /*
        |--------------------------------------------------------------------------
        | 6. LOG AKTIVITAS SISTEM (Hanya data lokal)
        |--------------------------------------------------------------------------
        */

        $log_siswa = User::where('role_id', 3)
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($user) {
                return [
                    'initial' => strtoupper(substr($user->name, 0, 2)),
                    'title' => 'Pendaftaran siswa baru',
                    'description' => $user->name . ' terdaftar sebagai siswa.',
                    'status' => 'Berhasil',
                    'type' => 'success',
                    'time' => $user->created_at,
                ];
            });

        $log_tutor = DedicatedTutor::latest()
            ->take(2)
            ->get()
            ->map(function ($item) {
                return [
                    'initial' => 'DT',
                    'title' => 'Permintaan dedicated tutor',
                    'description' => 'Permintaan tutor berstatus ' . $item->status . '.',
                    'status' => ucfirst($item->status),
                    'type' => $item->status === 'pending' ? 'warning' : 'info',
                    'time' => $item->created_at,
                ];
            });

        $log_aktivitas = collect()
            ->merge($log_siswa)
            ->merge($log_tutor)
            ->sortByDesc('time')
            ->take(5)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 7. INFORMASI & PROMOSI
        |--------------------------------------------------------------------------
        */

        $promo_aktif = Promotion::where('is_active', true)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->latest()
            ->take(2)
            ->get();

        $banner_aktif = Banner::where('is_active', true)
            ->orderBy('order_position')
            ->latest()
            ->take(2)
            ->get();

        $pengumuman_terbaru = Announcement::latest()
            ->take(2)
            ->get();

        $informasi_promosi = collect();

        foreach ($promo_aktif as $promo) {
            $informasi_promosi->push([
                'type' => 'Promo',
                'title' => 'Promo ' . $promo->code,
                'description' => 'Diskon ' . $promo->discount_percent . '% untuk pendaftaran.',
                'status' => 'Aktif',
                'date' => 'berakhir ' . Carbon::parse($promo->end_date)->translatedFormat('d M Y'),
                'image' => $promo->image_banner,
                'image_type' => 'promotion',
                'time' => $promo->created_at,
            ]);
        }

        foreach ($banner_aktif as $banner) {
            $informasi_promosi->push([
                'type' => 'Banner',
                'title' => $banner->title ?? 'Banner Aktif',
                'description' => $banner->description ?? 'Banner sedang aktif.',
                'status' => 'Terjadwal',
                'date' => 'urutan ' . $banner->order_position,
                'image' => $banner->image,
                'image_type' => 'banner',
                'time' => $banner->created_at,
            ]);
        }

        foreach ($pengumuman_terbaru as $announcement) {
            $informasi_promosi->push([
                'type' => 'Pengumuman',
                'title' => $announcement->title,
                'description' => $announcement->description,
                'status' => 'Aktif',
                'date' => $announcement->created_at->translatedFormat('d M Y'),
                'image' => $announcement->image,
                'image_type' => 'announcement',
                'time' => $announcement->created_at,
            ]);
        }

        $informasi_promosi = $informasi_promosi
            ->sortByDesc('time')
            ->take(3)
            ->values();

        /*
        |--------------------------------------------------------------------------
        | 8. CARD STATISTIK
        |--------------------------------------------------------------------------
        */

        $stat_cards = [
            [
                'title' => 'Total Siswa',
                'value' => $total_siswa,
                'icon' => 'fa-user-group',
                'badge' => ($persen_siswa >= 0 ? '+' : '') . $persen_siswa . '%',
                'badge_text' => 'vs bulan lalu',
                'badge_type' => $persen_siswa >= 0 ? 'success' : 'danger',
                'route' => route('admin.siswa.index'),
            ],
            [
                'title' => 'Total Pengajar',
                'value' => $total_pengajar,
                'icon' => 'fa-graduation-cap',
                'badge' => ($persen_pengajar >= 0 ? '+' : '') . $persen_pengajar . '%',
                'badge_text' => 'vs bulan lalu',
                'badge_type' => $persen_pengajar >= 0 ? 'success' : 'danger',
                'route' => route('admin.manajemen-pengajar.index'),
            ],
            [
                'title' => 'Kelas Aktif',
                'value' => $total_kelas_aktif,
                'icon' => 'fa-calendar-days',
                'badge' => 'Aktif',
                'badge_text' => 'jadwal berjalan',
                'badge_type' => 'info',
                'route' => route('admin.jadwal.index'),
            ],
            [
                'title' => 'Materi Tersedia',
                'value' => $total_materi_aktif,
                'icon' => 'fa-book-open',
                'badge' => 'Tersedia',
                'badge_text' => 'materi',
                'badge_type' => 'info',
                'route' => '#',
            ],
            [
                'title' => 'Tryout Tersedia',
                'value' => $total_tryout_aktif,
                'icon' => 'fa-clipboard-check',
                'badge' => 'Berjalan',
                'badge_text' => 'tryout',
                'badge_type' => 'info',
                'route' => '#',
            ],
            [
                'title' => 'Permintaan Tutor',
                'value' => $tutor_pending,
                'icon' => 'fa-headset',
                'badge' => $tutor_pending > 0 ? 'Perlu ditindaklanjuti' : 'Aman',
                'badge_text' => '',
                'badge_type' => $tutor_pending > 0 ? 'warning' : 'success',
                'route' => route('admin.tutor.index'),
            ],
        ];

        return view('admin.dashboard', compact(
            'total_siswa',
            'total_pengajar',
            'pendaftaran_pending',
            'tutor_pending',
            'total_kelas_aktif',
            'total_materi_aktif',
            'total_tryout_aktif',
            'stat_cards',
            'chart_labels',
            'chart_siswa_baru',
            'chart_aktivitas_harian',
            'chart_total_siswa',
            'distribusi_aktivitas',
            'total_distribusi',
            'tugas_menunggu',
            'total_tugas_menunggu',
            'log_aktivitas',
            'informasi_promosi'
        ));
    }

    /**
     * Ambil total materi dari Microservice Materi (Port 9001)
     */
    private function getTotalMateriFromMicroservice(): int
    {
        try {
            $goUrl = env('GO_MATERI_URL', 'http://localhost:9001');
            $response = Http::timeout(5)->get("$goUrl/api/materials");

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                return count($data);
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil data materi dari microservice: " . $e->getMessage());
        }

        return 0;
    }

    /**
     * Ambil total tryout dari Microservice Tryout (Port 9002)
     */
    private function getTotalTryoutFromMicroservice(): int
    {
        try {
            $goUrl = env('GO_TRYOUT_URL', 'http://localhost:9002');
            $response = Http::timeout(5)->get("$goUrl/api/tryouts");

            if ($response->successful()) {
                $data = $response->json()['data'] ?? [];
                return count($data);
            }
        } catch (\Exception $e) {
            Log::warning("Gagal mengambil data tryout dari microservice: " . $e->getMessage());
        }

        return 0;
    }

    public function galeri()
    {
        return view('admin.galeri.index');
    }

    public function pengumuman()
    {
        return view('admin.pengumuman.index');
    }
}
