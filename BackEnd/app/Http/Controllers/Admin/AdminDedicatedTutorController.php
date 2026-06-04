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
    /**
     * Menampilkan halaman utama manajemen tutor untuk Admin.
     * Fix: relasi 'material' di-load dengan try-catch terpisah
     * agar jika koneksi pgsql_materi gagal, halaman tetap bisa dibuka.
     */
    public function index()
    {
        // ✅ FIX 1: Load tutors tanpa relasi 'material' dulu
        // karena material pakai koneksi DB berbeda (pgsql_materi)
        // yang sering jadi penyebab Exception → redirect ke back()
        try {
            $tutors = DedicatedTutor::with(['student.user', 'teacher'])
                ->latest()
                ->get();
        } catch (Exception $e) {
            // Jika DB utama pun error, baru benar-benar gagal
            return back()->with('error', 'Gagal koneksi database utama: ' . $e->getMessage());
        }

        // ✅ FIX 2: Load relasi 'material' secara terpisah
        // Jika gagal (misal pgsql_materi tidak tersambung), lanjut saja
        // dengan material = null, halaman tetap terbuka
        foreach ($tutors as $tutor) {
            try {
                $tutor->load('material');
            } catch (Exception $e) {
                // Material gagal load — set null, jangan crash seluruh halaman
                $tutor->setRelation('material', null);
            }
        }

        // ✅ FIX 3: Load daftar pengajar tersedia
        try {
            $availableTeachers = User::whereHas('role', function ($q) {
                $q->where('role_name', 'pengajar');
            })->select('usersID', 'name')->get();
        } catch (Exception $e) {
            $availableTeachers = collect();
        }

        // ✅ FIX 4: Pre-load TeacherAssignment untuk hindari N+1 query
        // Dikelompokkan by "subject_name_classId" untuk lookup cepat di blade
        try {
            $teacherAssignments = TeacherAssignment::with('user')
                ->get()
                ->groupBy(fn($a) => $a->subject_name . '_' . $a->class_id);
        } catch (Exception $e) {
            $teacherAssignments = collect();
        }

        // ✅ Kirim semua variable ke view
        // Nama variable 'tutors' dipakai di blade (bukan 'availableTeachers' → 'allTeachers')
        return view('admin.dedicated_tutor.index', compact(
            'tutors',
            'availableTeachers',
            'teacherAssignments'
        ));
    }

    /**
     * Memproses keputusan Admin (Konfirmasi/Tolak) dan Penugasan Guru.
     */
    public function updateAssignment(Request $request, $id)
    {
        $request->validate([
            'status'     => 'required|in:confirmed,rejected',
            'teacher_id' => 'required_if:status,confirmed|nullable|exists:users,usersID',
        ], [
            'status.required'          => 'Status konfirmasi wajib dipilih.',
            'teacher_id.required_if'   => 'Admin wajib memilih Pengajar untuk permintaan yang disetujui.',
            'teacher_id.exists'        => 'Pengajar yang dipilih tidak terdaftar di sistem.',
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

    /**
     * Menghapus data pengajuan tutor.
     */
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