<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;

class ManajemenSiswaController extends Controller
{
    /**
     * 1. FITUR: SEMUA SISWA
     * Menampilkan semua orang yang sudah punya akun (Siswa)
     */
    public function index()
    {
        $siswas = User::where('role_id', 3)->with('student')->latest()->get();
        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * 2. FITUR: TAMBAH KELAS (Daftar Tunggu)
     * Menampilkan siswa yang baru daftar di HP dan statusnya masih PENDING
     */
    public function indexPendaftaran()
    {
        // Ambil data pendaftaran yang belum diaktivasi (status pending)
        $data = Enrollment::with(['user.student', 'classModel'])
                          ->where('status', 'pending')
                          ->latest()
                          ->get();

        return view('admin.siswa.pendaftaran', compact('data'));
    }

    /**
     * 3. DETAIL SISWA & FORM AKTIVASI
     * Menampilkan data lengkap satu siswa (A atau B) saat diklik
     */
    public function formAktivasi($id)
    {
        $enroll = Enrollment::with(['user.student', 'classModel'])->findOrFail($id);
        return view('admin.siswa.aktivasi_form', compact('enroll'));
    }

    /**
     * 4. PROSES AKTIVASI KE KELAS
     */
public function prosesAktivasi(Request $request, $id)
{
    // 1. Validasi agar input harus angka
    $request->validate([
        'durasi' => 'required|numeric'
    ]);

    $enroll = Enrollment::findOrFail($id);

    // 2. PROSES UPDATE (Tambahkan (int) di sini)
    $enroll->update([
        'status' => 'aktif',
        'expires_at' => now()->addDays((int) $request->durasi) // <--- PERBAIKAN DI SINI
    ]);

    return redirect()->route('admin.siswa.pendaftaran')->with('success', 'Siswa berhasil diaktifkan!');
}

public function storeJadwal(Request $request) {
    $request->validate([
        'class_id' => 'required',
        'teacher_id' => 'required',
        'title' => 'required',
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required',
    ]);

    \App\Models\Schedule::create($request->all());
    return back()->with('success', 'Jadwal berhasil diterbitkan!');
}
}
