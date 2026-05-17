<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\ClassModel;
use App\Models\User;
use App\Models\Material;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $nowTime = now()->format('H:i:s');

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
            }

            if ($request->status === 'finished') {
                $query->where(function ($q) use ($today, $nowTime) {
                    $q->whereDate('date', '<', $today)
                        ->orWhere(function ($sub) use ($today, $nowTime) {
                            $sub->whereDate('date', $today)
                                ->whereTime('end_time', '<', $nowTime);
                        });
                });
            }

            if ($request->status === 'scheduled') {
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
            ->paginate(6)
            ->withQueryString();

        $classes = ClassModel::orderBy('program_name')->get();

        $teachers = User::where('role_id', 2)
            ->orderBy('name')
            ->get();

        $totalJadwalBulanIni = Schedule::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        $jadwalHariIni = Schedule::whereDate('date', $today)->count();

        $kelasBerlangsung = Schedule::whereDate('date', $today)
            ->whereTime('start_time', '<=', $nowTime)
            ->whereTime('end_time', '>=', $nowTime)
            ->count();

        $jadwalSelesaiHariIni = Schedule::whereDate('date', $today)
            ->whereTime('end_time', '<', $nowTime)
            ->count();

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

        $scheduleCountByDate = Schedule::whereBetween('date', [
                $calendarStart->toDateString(),
                $calendarEnd->toDateString()
            ])
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
            'jadwal',
            'classes',
            'teachers',
            'totalJadwalBulanIni',
            'jadwalHariIni',
            'kelasBerlangsung',
            'jadwalSelesaiHariIni',
            'jadwalSelesaiTotal',
            'calendarMonth',
            'calendarDays'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'teacher_id' => 'required|exists:users,usersID',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        Schedule::create($validated);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $item = Schedule::findOrFail($id);
        $classes = ClassModel::orderBy('program_name')->get();
        $teachers = User::where('role_id', 2)->orderBy('name')->get();

        return view('admin.jadwal.edit', compact('item', 'classes', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $item = Schedule::findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,class_id',
            'teacher_id' => 'required|exists:users,usersID',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $item->update($validated);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus!');
    }

    public function getMateri($class_id)
    {
        $materi = Material::where('class_id', $class_id)
            ->select('title')
            ->distinct()
            ->orderBy('title')
            ->get();

        return response()->json($materi);
    }
}