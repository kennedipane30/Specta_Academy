<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
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

            // Ambil semua jadwal dari hari ini hingga 30 hari ke depan
            $schedules = DB::table('schedules')
                ->join('materials', 'schedules.subject_id', '=', 'materials.material_id')
                ->join('users', 'schedules.teacher_id', '=', 'users.usersID')
                ->where('schedules.class_id', $enrollment->class_id)
                ->whereDate('schedules.date', '>=', $todayStr)
                ->whereDate('schedules.date', '<=', $now->copy()->addDays(30)->toDateString())
                ->select(
                    'schedules.schedule_id as id',
                    'schedules.date',
                    'schedules.start_time',
                    'schedules.end_time',
                    'schedules.meeting_link',
                    'materials.material_name as subject_name',
                    'users.name as teacher_name'
                )
                ->orderBy('schedules.date', 'asc')
                ->orderBy('schedules.start_time', 'asc')
                ->get();

            $todaySchedules = [];
            $upcomingSchedules = [];

            foreach ($schedules as $item) {
                $start = Carbon::parse($item->date . ' ' . $item->start_time);
                $end   = Carbon::parse($item->date . ' ' . $item->end_time);

                $item->start_time = $start->format('H:i');
                $item->end_time   = $end->format('H:i');

                // Tambahkan field hari untuk keperluan UI Kelas Mendatang
                $item->day_name = Carbon::parse($item->date)->translatedFormat('l'); // Senin, Selasa
                $item->day_date = Carbon::parse($item->date)->format('d'); // 10, 11
                $item->month_name = strtoupper(Carbon::parse($item->date)->translatedFormat('M')); // JUN, JUL

                // Set Status
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

                // ✨ PROSES PENYORTIRAN OTOMATIS
                if ($item->date === $todayStr) {
                    $todaySchedules[] = $item;
                } else {
                    $upcomingSchedules[] = $item;
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

    public function index(Request $request)
    {
        // ... (Gunakan join ke materials juga jika ingin halaman 'Lihat Semua' aktif)
    }
}
