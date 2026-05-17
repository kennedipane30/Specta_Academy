<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Material;
use App\Models\Tryout;
use App\Models\DedicatedTutor;
use App\Models\Enrollment;
use App\Models\Promotion;
use App\Models\Banner;
use App\Models\Announcement;
use App\Models\Schedule;
use App\Models\TryoutSubmission;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | 1. STATISTIK UTAMA
        |--------------------------------------------------------------------------
        */

        $total_siswa = User::where('role_id', 3)->count();
        $total_pengajar = User::where('role_id', 2)->count();

        /*
         * Karena tabel classes belum punya kolom is_active/status,
         * kelas aktif sementara dihitung dari jadwal hari ini dan ke depan.
         */
        $total_kelas_aktif = Schedule::whereDate('date', '>=', $today)->count();

        /*
         * Karena tabel materials belum punya kolom status,
         * materi aktif sementara dihitung dari semua materi yang tersedia.
         */
        $total_materi_aktif = Material::count();

        /*
         * Karena tabel tryouts belum punya kolom status,
         * tryout aktif sementara dihitung dari semua tryout yang tersedia.
         */
        $total_tryout_aktif = Tryout::count();

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
        | 3. DATA GRAFIK 30 HARI TERAKHIR
        |--------------------------------------------------------------------------
        */

        $startDate = Carbon::today()->subDays(29);
        $endDate = Carbon::today();
        $period = CarbonPeriod::create($startDate, $endDate);

        $studentDaily = User::where('role_id', 3)
            ->whereDate('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $baseStudentCount = User::where('role_id', 3)
            ->whereDate('created_at', '<', $startDate)
            ->count();

        $enrollmentDaily = Enrollment::whereDate('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $tutorDaily = DedicatedTutor::whereDate('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $materialDaily = Material::whereDate('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $chart_labels = [];
        $chart_siswa_baru = [];
        $chart_aktivitas_harian = [];
        $chart_total_siswa = [];

        $runningTotalStudent = $baseStudentCount;

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');

            $newStudents = (int) ($studentDaily[$key] ?? 0);
            $dailyActivity =
                (int) ($enrollmentDaily[$key] ?? 0) +
                (int) ($tutorDaily[$key] ?? 0) +
                (int) ($materialDaily[$key] ?? 0);

            $runningTotalStudent += $newStudents;

            $chart_labels[] = $date->translatedFormat('d M');
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

        $materi_tanpa_file = Material::where(function ($query) {
                $query->whereNull('file_path')
                    ->orWhere('file_path', '');
            })
            ->count();

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
                'title' => 'Materi belum lengkap',
                'subtitle' => 'Materi belum memiliki file',
                'count' => $materi_tanpa_file,
                'icon' => 'fa-book-open',
                'route' => route('admin.assignments.index'),
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
        | 6. LOG AKTIVITAS SISTEM
        |--------------------------------------------------------------------------
        */

        $log_siswa = User::where('role_id', 3)
            ->latest()
            ->take(3)
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
            ->take(3)
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

        $log_materi = Material::latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'initial' => 'MT',
                    'title' => 'Materi diperbarui',
                    'description' => $item->title ?? 'Materi baru ditambahkan.',
                    'status' => 'Informasi',
                    'type' => 'info',
                    'time' => $item->created_at,
                ];
            });

        $log_tryout = Tryout::latest()
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'initial' => 'TO',
                    'title' => 'Tryout tersedia',
                    'description' => $item->title ?? 'Tryout baru tersedia.',
                    'status' => 'Aktif',
                    'type' => 'success',
                    'time' => $item->created_at,
                ];
            });

        $log_aktivitas = collect()
            ->merge($log_siswa)
            ->merge($log_tutor)
            ->merge($log_materi)
            ->merge($log_tryout)
            ->sortByDesc('time')
            ->take(4)
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
                'title' => 'Materi Aktif',
                'value' => $total_materi_aktif,
                'icon' => 'fa-book-open',
                'badge' => 'Tersedia',
                'badge_text' => 'materi',
                'badge_type' => 'info',
                'route' => route('admin.assignments.index'),
            ],
            [
                'title' => 'Tryout Aktif',
                'value' => $total_tryout_aktif,
                'icon' => 'fa-clipboard-check',
                'badge' => 'Berjalan',
                'badge_text' => 'tryout',
                'badge_type' => 'info',
                'route' => route('admin.tryout.index'),
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

    public function galeri()
    {
        return view('admin.galeri.index');
    }

    public function pengumuman()
    {
        return view('admin.pengumuman.index');
    }
}