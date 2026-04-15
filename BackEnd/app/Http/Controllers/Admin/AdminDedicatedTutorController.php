<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDedicatedTutorController extends Controller {

    public function index() {
        $tutors = DedicatedTutor::with(['student.user', 'teacher', 'material'])->latest()->get();
        $availableTeachers = User::where('role_id', 2)->get();

        return view('admin.dedicated_tutor.index', compact('tutors', 'availableTeachers'));
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

        return redirect()->route('admin.tutor.index')->with('success', 'Pengajuan berhasil diperbarui!');
    }
}
