<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\User; // Tambahkan ini
use Illuminate\Http\Request;

class AdminDedicatedTutorController extends Controller {

    public function index() {
        // Ambil data pengajuan dan data User yang rolenya pengajar untuk dropdown di Blade
        $tutors = DedicatedTutor::with(['student.user', 'teacher', 'material'])->latest()->get();

        // Asumsi pengajar dibedakan berdasarkan role_id atau logic tertentu
        $availableTeachers = User::where('role', 'pengajar')->get();

        return view('admin.dedicated_tutor.index', compact('tutors', 'availableTeachers'));
    }

    // Fungsi untuk Admin menugaskan Guru & Approve status
    public function updateAssignment(Request $request, $id) {
        $request->validate([
            'teacher_id' => 'required',
            'status' => 'required'
        ]);

        $tutor = DedicatedTutor::findOrFail($id);
        $tutor->update([
            'teacher_id' => $request->teacher_id,
            'status' => $request->status, // Biasanya diubah ke 'confirmed'
        ]);

        return back()->with('success', 'Guru berhasil ditugaskan dan status diperbarui');
    }
}
