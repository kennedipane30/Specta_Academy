<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\DedicatedTutor;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    public function index(): View
    {
        $teacherId = Auth::user()->usersID;
        $today = date('Y-m-d');

        // 1. Ambil Jadwal Mengajar Kelas (Reguler)
        $jadwalMendatang = Schedule::where('teacher_id', $teacherId)
                            ->whereDate('date', '>=', $today)
                            ->with('class')
                            ->orderBy('date', 'asc')
                            ->orderBy('start_time', 'asc')
                            ->get();

        // 2. Ambil Jadwal Dedicated Tutor (Privat)
        $jadwalTutor = DedicatedTutor::where('teacher_id', $teacherId)
                            ->where('status', 'confirmed')
                            ->whereDate('date', '>=', $today)
                            ->with(['student.user', 'material'])
                            ->orderBy('date', 'asc')
                            ->get();

        return view('pengajar.dashboard', compact('jadwalMendatang', 'jadwalTutor'));
    }
}
