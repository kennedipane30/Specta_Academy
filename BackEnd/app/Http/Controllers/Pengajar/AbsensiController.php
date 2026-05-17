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

class AbsensiController extends Controller
{
    /**
     * 1. Menampilkan daftar kelas + subjek yang ditugaskan ke pengajar.
     */
    public function index()
    {
        $teacherId = Auth::user()->usersID;
        $today = now()->toDateString();

        $assignments = TeacherAssignment::with('classModel')
            ->where('user_id', $teacherId)
            ->orderBy('class_id')
            ->orderBy('subject_name')
            ->get();

        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
            ->whereDate('date', $today)
            ->pluck('class_id')
            ->toArray();

        return view('pengajar.absensi.index', compact(
            'assignments',
            'jadwalHariIni'
        ));
    }

    /**
     * 2. Menampilkan daftar 20 minggu untuk kelas + subjek tertentu.
     */
    public function listWeeks($class_id, $subject)
    {
        $teacherId = Auth::user()->usersID;

        $assignment = TeacherAssignment::with('classModel')
            ->where('user_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->firstOrFail();

        $doneWeeks = Attendance::where('teacher_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->pluck('week')
            ->unique()
            ->values()
            ->toArray();

        return view('pengajar.absensi.weeks', [
            'class' => $assignment->classModel,
            'subject' => $subject,
            'doneWeeks' => $doneWeeks,
        ]);
    }

    /**
     * 3. Menampilkan form input absensi.
     */
    public function create($class_id, $subject, $week)
    {
        $teacherId = Auth::user()->usersID;

        TeacherAssignment::where('user_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->firstOrFail();

        $class = ClassModel::findOrFail($class_id);

        $siswas = Enrollment::where('class_id', $class_id)
            ->where('status', 'active')
            ->with('user.student')
            ->orderBy('user_id')
            ->get();

        $existingAttendance = Attendance::where('teacher_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->where('week', $week)
            ->pluck('status', 'user_id')
            ->toArray();

        return view('pengajar.absensi.show', compact(
            'class',
            'subject',
            'week',
            'siswas',
            'existingAttendance'
        ));
    }

    /**
     * 4. Menyimpan data absensi secara massal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'status' => 'required|array',
            'class_id' => 'required|integer',
            'subject_name' => 'required|string',
            'week' => 'required|integer|min:1|max:20',
        ]);

        $teacherId = Auth::user()->usersID;

        TeacherAssignment::where('user_id', $teacherId)
            ->where('class_id', $request->class_id)
            ->where('subject_name', $request->subject_name)
            ->firstOrFail();

        foreach ($request->status as $siswaID => $statusValue) {
            if (!in_array($statusValue, ['h', 'i', 'a'])) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'user_id' => $siswaID,
                    'class_id' => $request->class_id,
                    'subject_name' => $request->subject_name,
                    'week' => $request->week,
                ],
                [
                    'teacher_id' => $teacherId,
                    'status' => $statusValue,
                    'date' => now()->toDateString(),
                ]
            );
        }

        return redirect()
            ->route('pengajar.absensi.weeks', [
                $request->class_id,
                $request->subject_name,
            ])
            ->with('success', "Absensi Minggu ke-{$request->week} berhasil disimpan!");
    }

    /**
     * 5. Menampilkan rekapitulasi absensi.
     */
    public function showRecap($class_id, $subject, $week)
    {
        $teacherId = Auth::user()->usersID;

        TeacherAssignment::where('user_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->firstOrFail();

        $class = ClassModel::findOrFail($class_id);

        $data = Attendance::with('user')
            ->where('teacher_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->where('week', $week)
            ->orderBy('user_id')
            ->get();

        return view('pengajar.absensi.recap', compact(
            'class',
            'subject',
            'week',
            'data'
        ));
    }
}