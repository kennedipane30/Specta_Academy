<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\User;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Exception;

class AdminDedicatedTutorController extends Controller
{
    public function index()
    {
        try {
            // ✅ Tanpa relasi material
            $tutors = DedicatedTutor::with(['student.user', 'teacher'])
                ->latest()
                ->get();
        } catch (Exception $e) {
            return back()->with('error', 'Gagal koneksi database: ' . $e->getMessage());
        }

        try {
            $availableTeachers = User::whereHas('role', function ($q) {
                $q->where('role_name', 'pengajar');
            })->select('usersID', 'name')->get();
        } catch (Exception $e) {
            $availableTeachers = collect();
        }

        try {
            $teacherAssignments = TeacherAssignment::with('user')
                ->get()
                ->groupBy(fn($a) => $a->subject_name . '_' . $a->class_id);
        } catch (Exception $e) {
            $teacherAssignments = collect();
        }

        return view('admin.dedicated_tutor.index', compact(
            'tutors',
            'availableTeachers',
            'teacherAssignments'
        ));
    }

    public function updateAssignment(Request $request, $id)
    {
        $request->validate([
            'status'     => 'required|in:confirmed,rejected',
            'teacher_id' => 'required_if:status,confirmed|nullable|exists:users,usersID',
        ]);

        try {
            $tutor = DedicatedTutor::findOrFail($id);

            $tutor->update([
                'status'     => $request->status,
                'teacher_id' => ($request->status === 'confirmed') ? $request->teacher_id : null,
            ]);

            $studentName = $tutor->student->user->name ?? 'Siswa';

            return back()->with('success', "Berhasil! Status pengajuan {$studentName} telah diperbarui.");

        } catch (Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $tutor = DedicatedTutor::findOrFail($id);
            $tutor->delete();
            return back()->with('success', 'Data pengajuan berhasil dihapus.');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
