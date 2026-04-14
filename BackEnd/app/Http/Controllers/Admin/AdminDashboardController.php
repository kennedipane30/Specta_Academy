<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DedicatedTutor; // Tambahkan ini
use App\Models\Enrollment;     // Tambahkan ini
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Tampilan Utama Dashboard Admin
     */
    public function index()
    {
        // 1. Hitung Siswa Aktif (Role ID: 3)
        $total_siswa = User::where('role_id', 3)->count();

        // 2. Hitung Pengajar Aktif (Role ID: 2)
        $total_pengajar = User::where('role_id', 2)->count();

        // 3. Hitung Pendaftaran Kelas yang butuh Aktivasi/Verifikasi
        $pendaftaran_pending = Enrollment::where('status', 'pending')->count();

        // 4. Hitung Request Dedicated Tutor yang masih PENDING
        $tutor_pending = DedicatedTutor::where('status', 'pending')->count();

        // Mengirimkan semua data statistik ke view dashboard
        return view('admin.dashboard', compact(
            'total_siswa',
            'total_pengajar',
            'pendaftaran_pending',
            'tutor_pending'
        ));
    }

    /**
     * Halaman Manajemen Galeri
     */
    public function galeri()
    {
        return view('admin.galeri.index');
    }

    /**
     * Halaman Manajemen Pengumuman
     */
    public function pengumuman()
    {
        return view('admin.pengumuman.index');
    }
}
