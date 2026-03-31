<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
// Import model lain nanti (Payment, Course, dll)

class AdminController extends Controller
{
    public function dashboard()
    {
        // Mengambil statistik sederhana untuk dashboard
        $totalSiswa = User::where('role_id', 3)->count();
        $totalPengajar = User::where('role_id', 2)->count();

        return view('admin.dashboard', compact('totalSiswa', 'totalPengajar'));
    }

    public function dataSiswa()
    {
        $siswas = User::where('role_id', 3)->with('profile')->get();
        return view('admin.siswa.index', compact('siswas'));
    }
}
