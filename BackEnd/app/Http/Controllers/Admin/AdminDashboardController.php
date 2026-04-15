<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DedicatedTutor;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $total_siswa = User::where('role_id', 3)->count();
        $total_pengajar = User::where('role_id', 2)->count();
        $pendaftaran_pending = Enrollment::where('status', 'pending')->count();
        $tutor_pending = DedicatedTutor::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'total_siswa',
            'total_pengajar',
            'pendaftaran_pending',
            'tutor_pending'
        ));
    }

    public function galeri() { return view('admin.galeri.index'); }
    public function pengumuman() { return view('admin.pengumuman.index'); }
}
