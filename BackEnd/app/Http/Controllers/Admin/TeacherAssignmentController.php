<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAssignmentController extends Controller
{
    public function index()
    {
        $teachers = User::where('role_id', 2)->orderBy('name')->get();
        $classes = ClassModel::orderBy('program_name')->get();

        // Mengambil data dari tabel materials (sesuai migrasi Anda)
        $subjects = DB::table('materials')->orderBy('material_name')->get();

        // Ambil data penugasan (tanpa with subject karena subject merujuk ke tabel yang berbeda)
        $assignments = TeacherAssignment::with(['classModel', 'teacher'])->get();

        return view('admin.assignments.index', compact('teachers', 'classes', 'assignments', 'subjects'));
    }

    public function getSubjectsByClass($class_id)
    {
        $subjects = DB::table('materials')
                       ->where('class_id', $class_id)
                       ->orderBy('material_name')
                       ->get();

        return response()->json($subjects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,usersID',
            'class_id'   => 'required|exists:classes,class_id',
            'subject_id' => 'required|exists:materials,material_id'
        ]);

        $exists = TeacherAssignment::where([
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Mata pelajaran ini sudah memiliki pengajar di kelas tersebut.');
        }

        TeacherAssignment::create([
            'user_id'    => $request->teacher_id,
            'class_id'   => $request->class_id,
            'subject_id' => $request->subject_id
        ]);

        return back()->with('success', 'Pengajar berhasil ditugaskan!');
    }

    public function destroy($id)
    {
        TeacherAssignment::findOrFail($id)->delete();
        return back()->with('success', 'Penugasan berhasil dihapus.');
    }
}
