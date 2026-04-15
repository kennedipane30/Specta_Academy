<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalSiswa = User::where('role_id', 3)->count();
        $totalPengajar = User::where('role_id', 2)->count();

        return view('admin.dashboard', compact('totalSiswa', 'totalPengajar'));
    }

    public function dataSiswa()
    {
        // MODIFIKASI: relasi profile -> student sesuai model Student
        $siswas = User::where('role_id', 3)->with('student')->get();
        return view('admin.siswa.index', compact('siswas'));
    }
}
