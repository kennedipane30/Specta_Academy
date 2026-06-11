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
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    /**
     * ✅ LANGSUNG DARI TEACHER_ASSIGNMENTS (sudah ada subject_name)
     */
    private function getAssignmentsWithSubjects(): array
    {
        $userId = Auth::user()->usersID;

        $assignments = TeacherAssignment::with(['classModel'])
            ->where('user_id', $userId)
            ->get();

        $result = [];
        foreach ($assignments as $assignment) {
            $result[] = (object) [
                'class_id'     => $assignment->class_id,
                'classModel'   => $assignment->classModel,
                'subject_name' => $assignment->subject_name,
                'subject_id'   => $assignment->subject_id,
            ];
        }

        // Sort by class_id and subject_name
        usort($result, function($a, $b) {
            $keyA = $a->class_id . '-' . $a->subject_name;
            $keyB = $b->class_id . '-' . $b->subject_name;
            return strcmp($keyA, $keyB);
        });

        return $result;
    }

    /**
     * ✅ VALIDASI: Cek apakah pengajar memiliki penugasan
     */
    private function hasAssignment(int $classId, string $subjectName): bool
    {
        return TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $classId)
            ->where('subject_name', $subjectName)
            ->exists();
    }

    public function index()
    {
        $teacherId = Auth::user()->usersID;
        $today = now()->toDateString();

        $assignmentsWithSubjects = $this->getAssignmentsWithSubjects();

        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
            ->whereDate('date', $today)
            ->pluck('class_id')
            ->toArray();

        return view('pengajar.absensi.index', compact(
            'assignmentsWithSubjects',
            'jadwalHariIni'
        ));
    }

    public function listWeeks($class_id, $subject)
    {
        $teacherId = Auth::user()->usersID;

        if (!$this->hasAssignment($class_id, $subject)) {
            abort(403, 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        $class = ClassModel::findOrFail($class_id);

        $doneWeeks = Attendance::where('teacher_id', $teacherId)
            ->where('class_id', $class_id)
            ->where('subject_name', $subject)
            ->pluck('week')
            ->unique()
            ->values()
            ->toArray();

        return view('pengajar.absensi.weeks', [
            'class' => $class,
            'subject' => $subject,
            'doneWeeks' => $doneWeeks,
        ]);
    }

    public function create($class_id, $subject, $week)
    {
        $teacherId = Auth::user()->usersID;

        if (!$this->hasAssignment($class_id, $subject)) {
            abort(403, 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

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

    public function store(Request $request)
    {
        $request->validate([
            'status'       => 'required|array',
            'class_id'     => 'required|integer',
            'subject_name' => 'required|string',
            'week'         => 'required|integer|min:1|max:20',
        ]);

        $teacherId = Auth::user()->usersID;

        if (!$this->hasAssignment($request->class_id, $request->subject_name)) {
            return back()->with('error', 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

        foreach ($request->status as $siswaID => $statusValue) {
            if (!in_array($statusValue, ['h', 'i', 'a'])) {
                continue;
            }

            Attendance::updateOrCreate(
                [
                    'user_id'      => $siswaID,
                    'class_id'     => $request->class_id,
                    'subject_name' => $request->subject_name,
                    'week'         => $request->week,
                ],
                [
                    'teacher_id' => $teacherId,
                    'status'     => $statusValue,
                    'date'       => now()->toDateString(),
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

    public function showRecap($class_id, $subject, $week)
    {
        $teacherId = Auth::user()->usersID;

        if (!$this->hasAssignment($class_id, $subject)) {
            abort(403, 'Anda tidak ditugaskan untuk mata pelajaran ini.');
        }

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
