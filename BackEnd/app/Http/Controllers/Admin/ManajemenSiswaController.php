<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;

class ManajemenSiswaController extends Controller
{
    public function index()
    {
        $siswas = User::where('role_id', 3)->with('student')->latest()->get();
        return view('admin.siswa.index', compact('siswas'));
    }

    public function indexPendaftaran()
    {
        // classModel -> class
        $data = Enrollment::with(['user.student', 'class'])
                          ->where('status', 'pending')
                          ->latest()
                          ->get();

        return view('admin.siswa.pendaftaran', compact('data'));
    }

    public function formAktivasi($id)
    {
        $enroll = Enrollment::with(['user.student', 'class'])->findOrFail($id);
        return view('admin.siswa.aktivasi_form', compact('enroll'));
    }

    public function prosesAktivasi(Request $request, $id)
    {
        $request->validate(['durasi' => 'required|numeric']);

        $enroll = Enrollment::findOrFail($id);

        $enroll->update([
            'status' => 'active', // aktif -> active
            'expires_at' => now()->addDays((int) $request->durasi)
        ]);

        Student::where('user_id', $enroll->user_id)->update([
            'class_id' => $enroll->class_id
        ]);

        return redirect()->route('admin.siswa.pendaftaran')->with('success', 'Siswa berhasil diaktifkan!');
    }
}
