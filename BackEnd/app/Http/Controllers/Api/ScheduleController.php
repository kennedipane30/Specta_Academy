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
     * Ambil jadwal hari ini / terdekat (Dashboard Home)
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
                return response()->json(['status' => 'success', 'data' => [], 'label' => 'Jadwal Hari Ini']);
            }

            $now = Carbon::now();

            // 1. Ambil jadwal (Join ke MATERIALS bukan SUBJECTS)
            $schedules = DB::table('schedules')
                ->join('materials', 'schedules.subject_id', '=', 'materials.material_id') // SESUAIKAN DISINI
                ->join('users', 'schedules.teacher_id', '=', 'users.usersID')
                ->where('schedules.class_id', $enrollment->class_id)
                ->whereDate('schedules.date', '>=', $now->toDateString())
                ->whereDate('schedules.date', '<=', $now->copy()->addDays(14)->toDateString())
                ->select(
                    'schedules.schedule_id as id',
                    'schedules.date',
                    'schedules.start_time',
                    'schedules.end_time',
                    'schedules.meeting_link',
                    'materials.material_name as subject_name', // SESUAIKAN DISINI
                    'users.name as teacher_name'
                )
                ->orderBy('schedules.date', 'asc')
                ->orderBy('schedules.start_time', 'asc')
                ->get();

            $grouped = $schedules->groupBy('date');
            $nearestDate = null;

            // Cari tanggal terdekat yang kelasnya belum selesai
            foreach ($grouped as $date => $items) {
                $anyActive = $items->contains(function ($item) use ($now) {
                    $endTime = Carbon::parse($item->date . ' ' . $item->end_time);
                    return $now->lt($endTime);
                });

                if ($anyActive) {
                    $nearestDate = $date;
                    break;
                }
            }

            if (!$nearestDate) {
                $nearestDate = $grouped->keys()->first();
            }

            if (!$nearestDate) {
                return response()->json(['status' => 'success', 'data' => [], 'label' => 'Jadwal Hari Ini']);
            }

            // Penentuan Label
            $todayStr = $now->toDateString();
            $tomorrowStr = $now->copy()->addDay()->toDateString();

            if ($nearestDate === $todayStr) {
                $label = 'Jadwal Hari Ini';
            } elseif ($nearestDate === $tomorrowStr) {
                $label = 'Jadwal Besok';
            } else {
                $label = 'Jadwal ' . Carbon::parse($nearestDate)->translatedFormat('d M Y');
            }

            // Mapping Status & Format Waktu
            $data = $grouped[$nearestDate]->map(function ($item) use ($now) {
                $start = Carbon::parse($item->date . ' ' . $item->start_time);
                $end   = Carbon::parse($item->date . ' ' . $item->end_time);

                $item->start_time = $start->format('H:i');
                $item->end_time   = $end->format('H:i');

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
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        // ... (Gunakan join ke materials juga jika ingin halaman 'Lihat Semua' aktif)
    }
}
