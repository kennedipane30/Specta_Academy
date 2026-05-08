<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index()
    {
        // Ambil semua user dengan role_id 2 (Pengajar)
        $teachers = User::where('role_id', 2)->get();

        // Ambil semua kelas
        $classes = ClassModel::all();

        // Ambil data penugasan saat ini beserta relasinya
        $assignments = TeacherAssignment::with(['classModel'])->get();

        // Data Mata Pelajaran (Bisa dibuat dinamis jika perlu,
        // atau hardcoded sesuai seeder Anda)
        $subjects = ['TIU', 'TWK', 'English', 'Mathematics', 'Psychological Test', 'Physics', 'Biology', 'Chemistry'];

        return view('admin.assignments.index', compact('teachers', 'classes', 'assignments', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required',
            'class_id' => 'required',
            'subject_name' => 'required'
        ]);

        // Cek apakah penugasan yang sama sudah ada (Cegah duplikasi)
        $exists = TeacherAssignment::where([
            'user_id' => $request->teacher_id,
            'class_id' => $request->class_id,
            'subject_name' => $request->subject_name
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Pengajar ini sudah ditugaskan untuk materi tersebut di kelas ini.');
        }

        TeacherAssignment::create([
            'user_id' => $request->teacher_id,
            'class_id' => $request->class_id,
            'subject_name' => $request->subject_name
        ]);

        return back()->with('success', 'Penugasan berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        TeacherAssignment::findOrFail($id)->delete();
        return back()->with('success', 'Penugasan berhasil dihapus!');
    }
}
