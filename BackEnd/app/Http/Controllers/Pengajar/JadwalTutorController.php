<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use Illuminate\Support\Facades\Auth;

class JadwalTutorController extends Controller
{
    public function index()
    {
        // Menggunakan nested eager loading: student.user
        // Agar bisa ambil nama siswa dari tabel users lewat tabel students
        $jadwal = DedicatedTutor::with(['student.user', 'material'])
                    ->where('teacher_id', Auth::user()->userID) // Gunakan userID sesuai PK Anda
                    ->where('status', 'confirmed')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal_tutor.index', compact('jadwal'));
    }
}
