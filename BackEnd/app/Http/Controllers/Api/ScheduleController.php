<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\NewScheduleNotification;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Ambil jadwal hari ini / terdekat (Dashboard Home)
     * Logic: Jika jadwal hari ini sudah selesai semua, otomatis tampilkan jadwal besok.
     */
    public function today(Request $request)
    {
        try {
            $user = $request->user();
            $userId = $user->usersID ?? $user->id;

            $enrollment = DB::table('enrollments')
                ->where('user_id', $userId)
                ->where('status', 'active')
                ->first();

            if (!$enrollment) {
                return response()->json(['status' => 'success', 'data' => [], 'label' => 'Hari Ini']);
            }

            $now = Carbon::now();

            // 1. Ambil semua jadwal dalam 7 hari ke depan
            $schedules = DB::table('schedules')
                ->join('subjects', 'schedules.subject_id', '=', 'subjects.subject_id')
                ->join('users', 'schedules.teacher_id', '=', 'users.usersID')
                ->where('schedules.class_id', $enrollment->class_id)
                ->whereDate('schedules.date', '>=', $now->toDateString())
                ->whereDate('schedules.date', '<=', $now->copy()->addDays(7)->toDateString())
                ->select(
                    'schedules.schedule_id as id', 
                    'schedules.date',
                    'schedules.start_time',
                    'schedules.end_time',
                    'schedules.meeting_link',
                    'subjects.name as subject_name',
                    'users.name as teacher_name'
                )
                ->orderBy('schedules.date', 'asc')
                ->orderBy('schedules.start_time', 'asc')
                ->get();

            // 2. SMART LOGIC: Mencari tanggal terdekat yang belum selesai
            $grouped = $schedules->groupBy('date');
            $nearestDate = null;

            foreach ($grouped as $date => $items) {
                // Cek apakah ada jadwal di tanggal ini yang waktu selesainya (end_time) belum lewat
                $anyActive = $items->contains(function ($item) use ($now) {
                    $endTime = Carbon::parse($item->date . ' ' . $item->end_time);
                    return $now->lt($endTime); // 'now' masih lebih kecil dari 'waktu selesai'
                });

                if ($anyActive) {
                    $nearestDate = $date;
                    break; // Berhenti di tanggal pertama yang masih punya jadwal aktif
                }
            }

            // Fallback: Jika semua jadwal dalam 7 hari sudah selesai, ambil tanggal terakhir yang ada
            if (!$nearestDate) {
                $nearestDate = $grouped->keys()->first();
            }

            if (!$nearestDate) {
                return response()->json(['status' => 'success', 'data' => [], 'label' => 'Hari Ini']);
            }

            // 3. Penentuan Label Tanggal (Dinamis)
            $todayStr = $now->toDateString();
            $tomorrowStr = $now->copy()->addDay()->toDateString();

            if ($nearestDate === $todayStr) {
                $label = 'Hari Ini';
            } elseif ($nearestDate === $tomorrowStr) {
                $label = 'Besok';
            } else {
                $label = Carbon::parse($nearestDate)->translatedFormat('l, d M Y');
            }

            // 4. Mapping Status Jalannya Kelas
            $data = $grouped[$nearestDate]->map(function ($item) use ($now) {
                $start = Carbon::parse($item->date . ' ' . $item->start_time);
                $end   = Carbon::parse($item->date . ' ' . $item->end_time);

                if ($now->between($start, $end)) {
                    $item->status_label = 'SEDANG BERLANGSUNG';
                    $item->status_color = 'green';
                } elseif ($now->lt($start)) {
                    $item->status_label = 'TERJADWAL';
                    $item->status_color = 'blue';
                } else {
                    $item->status_label = 'SELESAI';
                    $item->status_color = 'grey';
                }
                return $item;
            });

            return response()->json([
                'status' => 'success',
                'label'  => $label,
                'data'   => $data->values(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil semua jadwal mendatang (Halaman Lihat Semua)
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
                return response()->json(['status' => 'success', 'data' => []]);
            }

            $schedules = DB::table('schedules')
                ->join('subjects', 'schedules.subject_id', '=', 'subjects.subject_id')
                ->join('users', 'schedules.teacher_id', '=', 'users.usersID')
                ->where('schedules.class_id', $enrollment->class_id)
                ->whereDate('schedules.date', '>=', Carbon::today())
                ->select(
                    'schedules.schedule_id as id',
                    'schedules.date',
                    'schedules.start_time',
                    'schedules.end_time',
                    'schedules.meeting_link',
                    'subjects.name as subject_name',
                    'users.name as teacher_name'
                )
                ->orderBy('schedules.date', 'asc')
                ->orderBy('schedules.start_time', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data'   => $schedules
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Kirim Notifikasi Manual
     */
    private function notifyStudents($scheduleId)
    {
        $schedule = DB::table('schedules')->where('schedule_id', $scheduleId)->first();
        if (!$schedule) return;

        $students = User::whereHas('classes', function($q) use ($schedule) {
            $q->where('enrollments.class_id', $schedule->class_id);
        })->get();

        foreach ($students as $student) {
            $student->notify(new NewScheduleNotification("Jadwal Baru: " . $schedule->title));
        }
    }
}