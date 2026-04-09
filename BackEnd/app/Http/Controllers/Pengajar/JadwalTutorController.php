<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use Illuminate\Support\Facades\Auth;

class JadwalTutorController extends Controller
{
    public function index()
    {
        // Hanya ambil yang teacher_id nya adalah user yang sedang login & statusnya confirmed
        $jadwal = DedicatedTutor::with(['student.user', 'material'])
                    ->where('teacher_id', Auth::id())
                    ->where('status', 'confirmed')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal_tutor.index', compact('jadwal'));
    }
}
