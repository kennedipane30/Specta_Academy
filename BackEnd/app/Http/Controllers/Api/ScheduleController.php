<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TeacherAssignment;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Ambil jadwal hari ini DAN kelas mendatang sekaligus
     */
    public function today(Request $request)
    {
        try {
            $user = $request->user();
            $userId = $user->usersID ?? $user->id;

            // Ambil kelas aktif siswa
            $enrollment = DB::table('enrollments')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'today' => [],
                        'upcoming' => []
                    ]
                ]);
            }

            $now = Carbon::now();
            $todayStr = $now->toDateString();

            // ✅ PERBAIKAN: Hapus 'schedules.meeting_link' karena kolom tidak ada
            $schedules = DB::table('schedules')
                ->join('users', 'schedules.teacher_id', '=', 'users.usersID')
                ->where('schedules.class_id', $enrollment->class_id)
                ->whereDate('schedules.date', '>=', $todayStr)
                ->whereDate('schedules.date', '<=', $now->copy()->addDays(30)->toDateString())
                ->select(
                    'schedules.schedule_id as id',
                    'schedules.class_id',
                    'schedules.subject_id',
                    'schedules.date',
                    'schedules.start_time',
                    'schedules.end_time',
                    'users.name as teacher_name'
                )
                ->orderBy('schedules.date', 'asc')
                ->orderBy('schedules.start_time', 'asc')
                ->get();

            $todaySchedules = [];
            $upcomingSchedules = [];

            foreach ($schedules as $item) {
                // Ambil nama mata pelajaran dari teacher_assignments
                $assignment = TeacherAssignment::where('class_id', $item->class_id)
                    ->where('subject_id', $item->subject_id)
                    ->first();

                $subjectName = $assignment ? $assignment->subject_name : 'Mata Pelajaran';

                $start = Carbon::parse($item->date . ' ' . $item->start_time);
                $end   = Carbon::parse($item->date . ' ' . $item->end_time);

                $scheduleData = [
                    'schedule_id' => $item->id,
                    'class_id' => $item->class_id,
                    'subject_name' => $subjectName,
                    'teacher_name' => $item->teacher_name,
                    'date' => $item->date,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $end->format('H:i'),
                    'day_name' => Carbon::parse($item->date)->translatedFormat('l'),
                    'day_date' => Carbon::parse($item->date)->format('d'),
                    'month_name' => strtoupper(Carbon::parse($item->date)->translatedFormat('M')),
                ];

                // Set Status
                if ($now->between($start, $end)) {
                    $scheduleData['status_label'] = 'SEDANG BERLANGSUNG';
                    $scheduleData['status_color'] = 'green';
                } elseif ($now->lt($start)) {
                    $scheduleData['status_label'] = 'TERJADWAL';
                    $scheduleData['status_color'] = 'blue';
                } else {
                    $scheduleData['status_label'] = 'SELESAI';
                    $scheduleData['status_color'] = 'grey';
                }

                if ($item->date === $todayStr) {
                    $todaySchedules[] = $scheduleData;
                } else {
                    $upcomingSchedules[] = $scheduleData;
                }
            }

            return response()->json([
                'status' => 'success',
                'data'   => [
                    'today' => $todaySchedules,
                    'upcoming' => $upcomingSchedules
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ambil semua jadwal (untuk halaman "Lihat Semua")
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $userId = $user->usersID ?? $user->id;

            $enrollment = DB::table('enrollments')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ]);
            }

            // ✅ PERBAIKAN: Hapus 'meeting_link'
            $schedules = DB::table('schedules')
                ->join('users', 'schedules.teacher_id', '=', 'users.usersID')
                ->where('schedules.class_id', $enrollment->class_id)
                ->whereDate('schedules.date', '>=', Carbon::today()->toDateString())
                ->select(
                    'schedules.schedule_id as id',
                    'schedules.class_id',
                    'schedules.subject_id',
                    'schedules.date',
                    'schedules.start_time',
                    'schedules.end_time',
                    'users.name as teacher_name'
                )
                ->orderBy('schedules.date', 'asc')
                ->orderBy('schedules.start_time', 'asc')
                ->paginate(20);

            $items = collect($schedules->items())->map(function($item) {
                $assignment = TeacherAssignment::where('class_id', $item->class_id)
                    ->where('subject_id', $item->subject_id)
                    ->first();

                $item->subject_name = $assignment ? $assignment->subject_name : 'Mata Pelajaran';
                $item->start_time = Carbon::parse($item->start_time)->format('H:i');
                $item->end_time = Carbon::parse($item->end_time)->format('H:i');
                $item->date_formatted = Carbon::parse($item->date)->translatedFormat('d F Y');

                return $item;
            });

            return response()->json([
                'status' => 'success',
                'data' => $items,
                'pagination' => [
                    'current_page' => $schedules->currentPage(),
                    'last_page' => $schedules->lastPage(),
                    'per_page' => $schedules->perPage(),
                    'total' => $schedules->total(),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function getStatusColor($date, $startTime, $endTime)
    {
        $now = Carbon::now();
        $scheduleStart = Carbon::parse($date . ' ' . $startTime);
        $scheduleEnd = Carbon::parse($date . ' ' . $endTime);

        if ($now->between($scheduleStart, $scheduleEnd)) {
            return 'green';
        } elseif ($now->greaterThan($scheduleEnd)) {
            return 'grey';
        } else {
            return 'blue';
        }
    }
}
