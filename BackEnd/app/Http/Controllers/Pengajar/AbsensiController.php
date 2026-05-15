<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\TeacherAssignment;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller {

    /**
     * 1. Menampilkan daftar Kelas + Subjek yang ditugaskan ke Pengajar
     */
    public function index() {
        $teacherId = Auth::user()->usersID;
        $today = date('Y-m-d');

        // Ambil data penugasan materi (Master Key)
        $assignments = TeacherAssignment::with('classModel')
                        ->where('user_id', $teacherId)
                        ->get();

        // Ambil ID kelas yang ada jadwal mengajarnya HARI INI untuk indikator "Jadwal Aktif"
        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
                            ->whereDate('date', $today)
                            ->pluck('class_id')
                            ->toArray();

        return view('pengajar.absensi.index', compact('assignments', 'jadwalHariIni'));
    }

    /**
     * 2. Menampilkan Grid 20 Minggu untuk subjek terpilih
     */
    public function listWeeks($class_id, $subject) {
        $assignments = TeacherAssignment::with('classModel')
                        ->where('class_id', $class_id)
                        ->where('subject_name', $subject)
                        ->firstOrFail();

        // Cari tahu minggu mana saja yang sudah pernah diabsen
        $doneWeeks = Attendance::where('class_id', $class_id)
                    ->where('subject_name', $subject)
                    ->pluck('week')
                    ->unique()
                    ->toArray();

        return view('pengajar.absensi.weeks', [
            'class' => $assignments->classModel,
            'subject' => $subject,
            'doneWeeks' => $doneWeeks
        ]);
    }

    /**
     * 3. Menampilkan Form Absensi (Fungsi yang tadi Error/Hilang)
     */
    public function create($class_id, $subject, $week) {
        $class = ClassModel::findOrFail($class_id);

        // Ambil daftar siswa yang status pendaftarannya 'active' di kelas ini
        $siswas = Enrollment::where('class_id', $class_id)
                    ->where('status', 'active')
                    ->with('user.student')
                    ->get();

        // Cek apakah data absen untuk minggu ini sudah ada (untuk keperluan edit jika perlu)
        $existingAttendance = Attendance::where('class_id', $class_id)
                                ->where('subject_name', $subject)
                                ->where('week', $week)
                                ->pluck('status', 'user_id')
                                ->toArray();

        return view('pengajar.absensi.show', compact('class', 'subject', 'week', 'siswas', 'existingAttendance'));
    }

    /**
     * 4. Menyimpan data absensi secara massal (Bulk Store)
     */
    public function store(Request $request) {
        $request->validate([
            'status' => 'required|array',
            'class_id' => 'required',
            'subject_name' => 'required',
            'week' => 'required'
        ]);

        $teacherId = Auth::user()->usersID;

        foreach ($request->status as $siswaID => $statusValue) {
            Attendance::updateOrCreate(
                [
                    'user_id'      => $siswaID,
                    'class_id'     => $request->class_id,
                    'subject_name' => $request->subject_name,
                    'week'         => $request->week,
                ],
                [
                    'teacher_id'   => $teacherId,
                    'status'       => $statusValue,
                    'date'         => now()->toDateString()
                ]
            );
        }

        return redirect()->route('pengajar.absensi.weeks', [$request->class_id, $request->subject_name])
                         ->with('success', "Absensi Minggu ke-{$request->week} berhasil disimpan!");
    }

    /**
     * 5. Menampilkan Rekapitulasi hasil absen yang sudah diisi
     */
    public function showRecap($class_id, $subject, $week) {
        $class = ClassModel::findOrFail($class_id);

        $data = Attendance::with('user')
                ->where('class_id', $class_id)
                ->where('subject_name', $subject)
                ->where('week', $week)
                ->get();

        return view('pengajar.absensi.recap', compact('class', 'subject', 'week', 'data'));
    }
}
