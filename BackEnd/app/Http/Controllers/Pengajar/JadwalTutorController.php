<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use Illuminate\Support\Facades\Auth;

class JadwalTutorController extends Controller
{
    public function index()
    {
        // Mengambil jadwal tutor khusus untuk guru yang login
        // 'student.user' digunakan agar guru bisa melihat NAMA SISWA
        $jadwal = DedicatedTutor::with(['student.user', 'material'])
                    ->where('teacher_id', Auth::user()->usersID) // DISESUAIKAN: usersID
                    ->where('status', 'confirmed')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal_tutor.index', compact('jadwal'));
    }
}
