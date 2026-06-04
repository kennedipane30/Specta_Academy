<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Subject; // Import Model Subject
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index()
    {
        // 1. Ambil semua user dengan role_id 2 (Pengajar)
        $teachers = User::where('role_id', 2)->orderBy('name')->get();

        // 2. Ambil semua kelas
        $classes = ClassModel::orderBy('program_name')->get();

        // 3. Ambil data Mata Pelajaran dari Database (Bukan Hardcoded lagi)
        $subjects = Subject::orderBy('name')->get();

        // 4. Ambil data penugasan saat ini dengan relasi lengkap
        // Pastikan di model TeacherAssignment sudah ada relasi 'teacher', 'classModel', dan 'subject'
        $assignments = TeacherAssignment::with(['classModel', 'teacher', 'subject'])->get();

        return view('admin.assignments.index', compact('teachers', 'classes', 'assignments', 'subjects'));
    }

    public function store(Request $request)
    {
        // Validasi menggunakan subject_id
        $request->validate([
            'teacher_id' => 'required|exists:users,usersID',
            'class_id'   => 'required|exists:classes,class_id',
            'subject_id' => 'required|exists:subjects,subject_id'
        ]);

        // Cek apakah penugasan yang sama sudah ada (Cegah duplikasi guru di mapel & kelas yang sama)
        $exists = TeacherAssignment::where([
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Mata pelajaran ini sudah memiliki pengajar di kelas tersebut. Hapus penugasan lama jika ingin mengganti pengajar.');
        }

        // Simpan penugasan baru
        TeacherAssignment::create([
            'user_id'    => $request->teacher_id,
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id
        ]);

        return back()->with('success', 'Pengajar berhasil ditugaskan ke Matrix Kurikulum!');
    }

    public function destroy($id)
    {
        // Hapus penugasan dari Matrix
        TeacherAssignment::findOrFail($id)->delete();
        
        return back()->with('success', 'Penugasan berhasil dihapus. Slot di Matrix sekarang kosong.');
    }
}