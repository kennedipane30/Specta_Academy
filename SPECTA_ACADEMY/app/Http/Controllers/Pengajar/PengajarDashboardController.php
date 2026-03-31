<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Schedule;
use App\Models\Attendance; // Pastikan Model Attendance sudah dibuat
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    /**
     * Dashboard Utama Pengajar
     */
    public function index(): View
    {
        return view('pengajar.dashboard');
    }

    /**
     * Menampilkan daftar 4 kelas untuk di-absen
     * MODIFIKASI: Menambahkan pengecekan jadwal hari ini
     */
    public function absensi(): View
{
    $classes = \App\Models\ClassModel::all();
    $teacherId = Auth::id(); // Integritas Aktor: Siapa yang sedang login?
    $today = \Carbon\Carbon::now()->toDateString(); // Integritas Waktu: Tanggal hari ini di server.

    // Hanya ambil ID kelas jika Guru ini MEMILIKI JADWAL di hari ini
    $jadwalHariIni = \App\Models\Schedule::where('teacher_id', $teacherId)
                            ->whereDate('date', $today)
                            ->pluck('class_id')
                            ->toArray();

    return view('pengajar.absensi.index', compact('classes', 'jadwalHariIni'));
}

    /**
     * Menampilkan Daftar Siswa di Kelas Tertentu
     * MODIFIKASI: Menambahkan Security Check agar hanya guru yang bertugas yang bisa buka
     */
    public function showAbsensi($class_id): View | \Illuminate\Http\RedirectResponse
{
    // 1. Cek apakah Guru ini terjadwal di kelas ini HARI INI
    $isAssigned = Schedule::where('class_id', $class_id)
                        ->where('teacher_id', Auth::id())
                        ->where('date', date('Y-m-d'))
                        ->first();

    // MODIFIKASI: Jika tidak ada jadwal, balikkan ke halaman sebelumnya dengan pesan
    if (!$isAssigned) {
        return redirect()->route('pengajar.absensi.index')
                         ->with('info', 'Tidak ada absensi untuk kelas ini hari ini.');
    }

    $class = ClassModel::findOrFail($class_id);

    $siswas = Enrollment::where('class_id', $class_id)
                ->where('status', 'aktif')
                ->where('expires_at', '>', now())
                ->with(['user.student'])
                ->get();

    return view('pengajar.absensi.show', compact('siswas', 'class', 'isAssigned'));
}
    /**
     * FUNGSI BARU: Simpan Absensi ke Database
     */
    public function storeAbsensi(Request $request)
    {
        // Validasi input absensi
        foreach ($request->status as $usersID => $status) {
            Attendance::create([
                'schedule_id' => $request->schedule_id,
                'user_id'     => $usersID,
                'status'      => $status,
                'date'        => date('Y-m-d')
            ]);
        }

        return redirect()->route('pengajar.absensi.index')->with('success', 'Absensi berhasil disimpan!');
    }

    /**
     * Menampilkan Jadwal Mengajar milik pengajar yang sedang login
     */
    public function jadwalSaya(): View
    {
        $jadwal = Schedule::where('teacher_id', Auth::id())
                    ->with('classModel')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal.index', compact('jadwal'));
    }
}
