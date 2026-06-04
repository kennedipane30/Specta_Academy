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
    public function index(Request $request)
    {
        $today = Carbon::today();
        $nowTime = now()->format('H:i:s');

        $query = Schedule::with(['class', 'teacher', 'subject']);

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
                        // MODIFIKASI: Gunakan material_name sesuai database Anda
                        $subQuery->whereRaw('LOWER(material_name) LIKE ?', ["%{$search}%"]);
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

        $calendarMonth = Carbon::today()->startOfMonth();
        $calendarStart = $calendarMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $scheduleCountByDate = Schedule::whereBetween('date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->selectRaw('date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        $calendarDays = collect(CarbonPeriod::create($calendarStart, $calendarEnd))
            ->map(function ($date) use ($calendarMonth, $scheduleCountByDate) {
                /** @var \Carbon\Carbon $date */ // ✨ Tambahkan baris ini untuk menghilangkan error di IDE

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id'     => 'required|exists:classes,class_id',
            'subject_id'   => 'required|exists:materials,material_id', // ✨ Pastikan ini merujuk ke material_id
            'teacher_id'   => 'required|exists:users,usersID',
            'title'        => 'required|string|max:255',
            'date'         => 'required|date',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
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
            // MODIFIKASI: Gunakan join ke tabel 'materials' dan kolom 'material_name'
            $subjects = DB::table('teacher_assignments')
                ->join('materials', 'teacher_assignments.subject_id', '=', 'materials.material_id')
                ->where('teacher_assignments.class_id', $class_id)
                ->select('materials.material_id as subject_id', 'materials.material_name as name')
                ->get();

            if ($subjects->isEmpty()) {
                return response()->json([]);
            }

            return response()->json($subjects);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTeacherBySubject($class_id, $subject_id)
    {
        try {
            $teacher = DB::table('teacher_assignments')
                ->join('users', 'teacher_assignments.user_id', '=', 'users.usersID')
                ->where('teacher_assignments.class_id', $class_id)
                ->where('teacher_assignments.subject_id', $subject_id)
                ->select('users.usersID as teacher_id', 'users.name as teacher_name')
                ->first();

            return response()->json($teacher);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }

    // ... API Method tetap sama
}
