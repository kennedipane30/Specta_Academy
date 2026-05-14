<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\User;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class AdminDedicatedTutorController extends Controller {

    public function index() {
        // Ambil data tutor dengan relasi lengkap
        $tutors = DedicatedTutor::with(['student.user', 'teacher', 'material'])->latest()->get();

        return view('admin.dedicated_tutor.index', compact('tutors'));
    }

    public function updateAssignment(Request $request, $id) {
        $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'teacher_id' => 'required_if:status,confirmed'
        ]);

        $tutor = DedicatedTutor::findOrFail($id);

        $tutor->update([
            'status' => $request->status,
            'teacher_id' => ($request->status == 'confirmed') ? $request->teacher_id : null,
        ]);

        // ✨ PERBAIKAN: Gunakan back() agar tetap di halaman yang sama dan memunculkan alert
        return back()->with('success', 'Tutor assignment updated successfully!');
    }
}
