<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\TeacherAssignment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $nowTime = now()->format('H:i:s');

        // ✅ Hapus 'subject' dari with
        $query = Schedule::with(['class', 'teacher']);

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('class', function ($classQuery) use ($search) {
                        $classQuery->whereRaw('LOWER(program_name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'ongoing') {
                $query->whereDate('date', $today)
                    ->whereTime('start_time', '<=', $nowTime)
                    ->whereTime('end_time', '>=', $nowTime);
            } elseif ($request->status === 'finished') {
                $query->where(function ($q) use ($today, $nowTime) {
                    $q->whereDate('date', '<', $today)
                        ->orWhere(function ($sub) use ($today, $nowTime) {
                            $sub->whereDate('date', $today)
                                ->whereTime('end_time', '<', $nowTime);
                        });
                });
            } elseif ($request->status === 'scheduled') {
                $query->where(function ($q) use ($today, $nowTime) {
                    $q->whereDate('date', '>', $today)
                        ->orWhere(function ($sub) use ($today, $nowTime) {
                            $sub->whereDate('date', $today)
                                ->whereTime('start_time', '>', $nowTime);
                        });
                });
            }
        }

        $jadwal = $query
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        // ✅ Untuk setiap jadwal, ambil nama mata pelajaran dari teacher_assignments
        foreach ($jadwal as $item) {
            $assignment = TeacherAssignment::where('class_id', $item->class_id)
                ->where('subject_id', $item->subject_id)
                ->first();
            $item->subject_name = $assignment ? $assignment->subject_name : $item->title;
        }

        $classes = ClassModel::orderBy('program_name')->get();

        $totalJadwalBulanIni = Schedule::whereYear('date', now()->year)->whereMonth('date', now()->month)->count();
        $jadwalHariIni = Schedule::whereDate('date', $today)->count();
        $kelasBerlangsung = Schedule::whereDate('date', $today)
            ->whereTime('start_time', '<=', $nowTime)
            ->whereTime('end_time', '>=', $nowTime)->count();
        $jadwalSelesaiTotal = Schedule::where(function ($q) use ($today, $nowTime) {
            $q->whereDate('date', '<', $today)
                ->orWhere(function ($sub) use ($today, $nowTime) {
                    $sub->whereDate('date', $today)
                        ->whereTime('end_time', '<', $nowTime);
                });
        })->count();

        // ✅ PERBAIKAN: Calendar Days dengan error handling
        $calendarMonth = Carbon::today()->startOfMonth();
        $calendarStart = $calendarMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $scheduleCountByDate = Schedule::whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        // ✅ PERBAIKAN: Gunakan loop manual untuk menghindari error CarbonPeriod
        $calendarDays = [];
        $currentDate = $calendarStart->copy();

        while ($currentDate <= $calendarEnd) {
            $key = $currentDate->toDateString();
            $calendarDays[] = [
                'date' => $key,
                'day' => $currentDate->day,
                'is_current_month' => $currentDate->month === $calendarMonth->month,
                'is_today' => $currentDate->isToday(),
                'schedule_count' => $scheduleCountByDate[$key] ?? 0,
            ];
            $currentDate->addDay();
        }

        return view('admin.jadwal.index', compact(
            'jadwal', 'classes', 'totalJadwalBulanIni',
            'jadwalHariIni', 'kelasBerlangsung',
            'jadwalSelesaiTotal', 'calendarMonth', 'calendarDays'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'     => 'required|exists:classes,class_id',
            'subject_id'   => 'nullable|string',
            'teacher_id'   => 'required|exists:users,usersID',
            'title'        => 'required|string|max:255',
            'date'         => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
        ]);

        Schedule::create($validated);
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dipublikasikan!');
    }

    /**
     * ✅ AJAX: Ambil Mata Pelajaran dari teacher_assignments
     */
    public function getSubjects($class_id)
    {
        try {
            $subjects = TeacherAssignment::where('class_id', $class_id)
                ->whereNotNull('subject_name')
                ->select('subject_id', 'subject_name as name')
                ->distinct()
                ->get();

            if ($subjects->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error("Error getSubjects: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * ✅ AJAX: Ambil teacher berdasarkan class_id dan subject_id
     */
    public function getTeacherBySubject($class_id, $subject_id)
    {
        try {
            $teacher = TeacherAssignment::join('users', 'teacher_assignments.user_id', '=', 'users.usersID')
                ->where('teacher_assignments.class_id', $class_id)
                ->where('teacher_assignments.subject_id', $subject_id)
                ->select('users.usersID as teacher_id', 'users.name as teacher_name')
                ->first();

            return response()->json($teacher);
        } catch (\Exception $e) {
            Log::error("Error getTeacherBySubject: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }

    /**
     * API untuk calendar view
     */
    public function getCalendarData(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $schedules = Schedule::with(['class', 'teacher'])
            ->whereBetween('date', [$start, $end])
            ->get()
            ->map(function($schedule) {
                $assignment = TeacherAssignment::where('class_id', $schedule->class_id)
                    ->where('subject_id', $schedule->subject_id)
                    ->first();
                $subjectName = $assignment ? $assignment->subject_name : $schedule->title;

                return [
                    'id' => $schedule->schedule_id,
                    'title' => $subjectName,
                    'start' => $schedule->date . 'T' . $schedule->start_time,
                    'end' => $schedule->date . 'T' . $schedule->end_time,
                    'className' => 'schedule-event',
                    'extendedProps' => [
                        'class_name' => $schedule->class->program_name ?? '',
                        'teacher_name' => $schedule->teacher->name ?? '',
                        'subject_name' => $subjectName,
                    ]
                ];
            });

        return response()->json($schedules);
    }

        /**
     * Show the form for editing the specified schedule.
     */
    public function edit($id)
    {
        $schedule = Schedule::with(['class', 'teacher'])->findOrFail($id);

        $classes = ClassModel::orderBy('program_name')->get();

        $subjects = TeacherAssignment::where('class_id', $schedule->class_id)
            ->whereNotNull('subject_name')
            ->select('subject_id', 'subject_name as name')
            ->distinct()
            ->get();

        return view('admin.jadwal.edit', compact('schedule', 'classes', 'subjects'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'class_id'     => 'required|exists:classes,class_id',
            'subject_id'   => 'nullable|string',
            'teacher_id'   => 'required|exists:users,usersID',
            'title'        => 'required|string|max:255',
            'date'         => 'required|date|after_or_equal:today',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update($validated);

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }
}
