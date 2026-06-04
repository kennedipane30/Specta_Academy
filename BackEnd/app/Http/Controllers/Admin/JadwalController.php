<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\Subject;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{
    /**
     * TAMPILAN DASHBOARD JADWAL (WEB ADMIN)
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $nowTime = now()->format('H:i:s');

        // Memuat relasi agar nama kelas, pengajar, dan mapel muncul di tabel
        $query = Schedule::with(['class', 'teacher', 'subject']);

        // --- FILTER PENCARIAN ---
        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('class', function ($classQuery) use ($search) {
                        $classQuery->whereRaw('LOWER(program_name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('teacher', function ($teacherQuery) use ($search) {
                        $teacherQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    })
                    ->orWhereHas('subject', function ($subQuery) use ($search) {
                        $subQuery->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        // --- FILTER STATUS JADWAL ---
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

        $classes = ClassModel::orderBy('program_name')->get();

        // Logika Statistik
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

        // Logika Kalender
        $calendarMonth = Carbon::today()->startOfMonth();
        $calendarStart = $calendarMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $scheduleCountByDate = Schedule::whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $calendarDays = collect(CarbonPeriod::create($calendarStart, $calendarEnd))
            ->map(function ($date) use ($calendarMonth, $scheduleCountByDate) {
                $key = $date->toDateString();
                return [
                    'date' => $key,
                    'day' => $date->day,
                    'is_current_month' => $date->month === $calendarMonth->month,
                    'is_today' => $date->isToday(),
                    'schedule_count' => $scheduleCountByDate[$key] ?? 0,
                ];
            });

        return view('admin.jadwal.index', compact(
            'jadwal', 'classes', 'totalJadwalBulanIni',
            'jadwalHariIni', 'kelasBerlangsung',
            'jadwalSelesaiTotal', 'calendarMonth', 'calendarDays'
        ));
    }

    /**
     * SIMPAN JADWAL BARU
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'   => 'required|exists:classes,class_id',
            'subject_id' => 'required|exists:subjects,subject_id',
            'teacher_id' => 'required|exists:users,usersID',
            'title'      => 'required|string|max:255',
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
            'meeting_link' => 'nullable|url'
        ]);

        Schedule::create($validated);
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dipublikasikan!');
    }

    /**
     * 🔥 AJAX: Ambil Mata Pelajaran dari Matrix Penugasan
     */
   public function getSubjects($class_id)
{
    try {
        // Ambil mapel dari matrix. 
        // Pastikan nama tabel 'teacher_assignments' dan 'subjects' sudah benar.
        $subjects = DB::table('teacher_assignments')
            ->join('subjects', 'teacher_assignments.subject_id', '=', 'subjects.subject_id')
            ->where('teacher_assignments.class_id', $class_id)
            ->select('subjects.subject_id', 'subjects.name')
            ->get();

        // Jika data kosong, berikan log agar admin tahu
        if ($subjects->isEmpty()) {
            return response()->json(['error' => 'Belum ada mapel di Matrix untuk kelas ini'], 404);
        }

        return response()->json($subjects);
    } catch (\Exception $e) {
        // Balas dengan pesan error database yang spesifik
        return response()->json(['error' => $e->getMessage()], 500);
    }
} /**
     * 🔥 AJAX: Ambil Pengajar Otomatis dari Matrix Berdasarkan Mapel yang dipilih
     */
    public function getTeacherBySubject($class_id, $subject_id)
    {
        try {
            // Mencari siapa guru yang ditugaskan di Matrix untuk Kelas & Mapel tsb
            $teacher = DB::table('teacher_assignments')
                ->join('users', 'teacher_assignments.user_id', '=', 'users.usersID')
                ->where('teacher_assignments.class_id', $class_id)
                ->where('teacher_assignments.subject_id', $subject_id)
                ->select('users.usersID as teacher_id', 'users.name as teacher_name')
                ->first();

            if (!$teacher) {
                return response()->json(['error' => 'Belum ada guru ditugaskan di Matrix'], 404);
            }

            return response()->json($teacher);
        } catch (\Exception $e) {
            Log::error("Gagal ambil Guru: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }

    /**
     * 📱 API SISWA: Melihat jadwal berdasarkan kelas yang dibeli
     */
    public function getJadwalSiswa(Request $request)
    {
        $user = $request->user();
        
        // Ambil kelas yang dibeli (Enrollment) yang statusnya aktif
        $myClasses = DB::table('enrollments')
            ->where('user_id', $user->usersID)
            ->where('status', 'active')
            ->pluck('class_id');

        $data = Schedule::whereIn('class_id', $myClasses)
            ->with(['class', 'teacher', 'subject'])
            ->orderBy('date', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * 📱 API GURU: Melihat jadwal mengajar dia sendiri
     */
    public function getJadwalGuru(Request $request)
    {
        $user = $request->user();

        $data = Schedule::where('teacher_id', $user->usersID)
            ->with(['class', 'subject'])
            ->orderBy('date', 'asc')
            ->get();

        return response()->json(['status' => 'success', 'data' => $data]);
    }
}