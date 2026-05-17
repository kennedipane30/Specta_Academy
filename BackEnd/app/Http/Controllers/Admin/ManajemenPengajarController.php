<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TeacherAssignment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ManajemenPengajarController extends Controller
{
    public function index(Request $request)
    {
        $teacherQuery = User::where('role_id', 2)
            ->with(['assignments.classModel']);

        if ($request->filled('search')) {
            $search = strtolower($request->search);

            $teacherQuery->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('assignments', function ($assignmentQuery) use ($search) {
                        $assignmentQuery->whereRaw('LOWER(subject_name) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        if ($request->filled('subject_name')) {
            $teacherQuery->whereHas('assignments', function ($assignmentQuery) use ($request) {
                $assignmentQuery->where('subject_name', $request->subject_name);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $teacherQuery->where('is_verified', true);
            }

            if ($request->status === 'inactive') {
                $teacherQuery->where('is_verified', false);
            }
        }

        $teachers = $teacherQuery
            ->latest('created_at')
            ->paginate(7)
            ->withQueryString();

        $teacherIds = $teachers->getCollection()->pluck('usersID');

        $assignmentMap = TeacherAssignment::with('classModel')
            ->whereIn('user_id', $teacherIds)
            ->get()
            ->groupBy('user_id');

        $scheduleCountMap = Schedule::whereIn('teacher_id', $teacherIds)
            ->whereDate('date', '>=', now()->toDateString())
            ->select('teacher_id', DB::raw('COUNT(DISTINCT class_id) as total'))
            ->groupBy('teacher_id')
            ->pluck('total', 'teacher_id');

        $subjects = TeacherAssignment::whereNotNull('subject_name')
            ->where('subject_name', '!=', '')
            ->distinct()
            ->orderBy('subject_name')
            ->pluck('subject_name');

        $totalPengajar = User::where('role_id', 2)->count();

        $pengajarAktif = User::where('role_id', 2)
            ->where('is_verified', true)
            ->count();

        $pengajarBaruBulanIni = User::where('role_id', 2)
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();

        $pengajarBulanLalu = User::where('role_id', 2)
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->count();

        $growthPengajar = $this->growthPercent($pengajarBaruBulanIni, $pengajarBulanLalu);

        $kelasDiajar = TeacherAssignment::distinct()->count('class_id');

        $distribusiBidang = TeacherAssignment::select(
                'subject_name',
                DB::raw('COUNT(*) as total')
            )
            ->whereNotNull('subject_name')
            ->where('subject_name', '!=', '')
            ->groupBy('subject_name')
            ->orderByDesc('total')
            ->get();

        $totalDistribusiBidang = $distribusiBidang->sum('total');

        $aktivitasPengajar = User::where('role_id', 2)
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($teacher) {
                return [
                    'icon' => 'fa-user-plus',
                    'title' => $teacher->name,
                    'description' => 'Ditambahkan sebagai pengajar',
                    'time' => $teacher->created_at,
                ];
            });

        $aktivitasAssignment = TeacherAssignment::with(['user', 'classModel'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($assignment) {
                return [
                    'icon' => 'fa-book-open',
                    'title' => $assignment->user->name ?? 'Pengajar',
                    'description' => 'Ditugaskan mengajar ' . ($assignment->subject_name ?? 'materi'),
                    'time' => $assignment->created_at,
                ];
            });

        $aktivitasJadwal = Schedule::with(['teacher', 'class'])
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($schedule) {
                return [
                    'icon' => 'fa-calendar-days',
                    'title' => $schedule->teacher->name ?? 'Pengajar',
                    'description' => 'Jadwal mengajar diperbarui',
                    'time' => $schedule->created_at,
                ];
            });

        $aktivitasTerbaru = collect()
            ->merge($aktivitasPengajar)
            ->merge($aktivitasAssignment)
            ->merge($aktivitasJadwal)
            ->sortByDesc('time')
            ->take(5)
            ->values();

        return view('admin.pengajar.index', compact(
            'teachers',
            'subjects',
            'assignmentMap',
            'scheduleCountMap',
            'totalPengajar',
            'pengajarAktif',
            'pengajarBaruBulanIni',
            'growthPengajar',
            'kelasDiajar',
            'distribusiBidang',
            'totalDistribusiBidang',
            'aktivitasTerbaru'
        ));
    }

    public function create()
    {
        return view('admin.pengajar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'phone'       => 'required|string|max:30',
            'password'    => 'required|string|min:6',
            'is_verified' => 'required|in:0,1',
        ]);

        User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'password'    => Hash::make($request->password),
            'role_id'     => 2,
            'is_verified' => (bool) $request->is_verified,
        ]);

        return redirect()
            ->route('admin.manajemen-pengajar.index')
            ->with('success', 'Akun pengajar berhasil didaftarkan!');
    }

    public function edit($id)
    {
        $teacher = User::where('role_id', 2)->findOrFail($id);

        return view('admin.pengajar.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $teacher = User::where('role_id', 2)->findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($teacher->usersID, 'usersID'),
            ],
            'phone'       => 'required|string|max:30',
            'password'    => 'nullable|string|min:6',
            'is_verified' => 'required|in:0,1',
        ]);

        $payload = [
            'name'        => $request->name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'is_verified' => (bool) $request->is_verified,
        ];

        if ($request->filled('password')) {
            $payload['password'] = Hash::make($request->password);
        }

        $teacher->update($payload);

        return redirect()
            ->route('admin.manajemen-pengajar.index')
            ->with('success', 'Data pengajar berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $teacher = User::where('role_id', 2)->findOrFail($id);
        $teacher->delete();

        return redirect()
            ->route('admin.manajemen-pengajar.index')
            ->with('success', 'Akun pengajar berhasil dihapus!');
    }

    private function growthPercent($current, $previous)
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}