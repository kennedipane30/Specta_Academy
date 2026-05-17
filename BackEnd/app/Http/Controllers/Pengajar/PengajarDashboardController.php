<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\Material;
use App\Models\PracticeQuestion;
use App\Models\Schedule;
use App\Models\TeacherAssignment;
use App\Models\TryoutSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    public function index(): View
    {
        $teacherId = Auth::user()->usersID;
        $today = Carbon::today();

        $assignments = TeacherAssignment::with('classModel')
            ->where('user_id', $teacherId)
            ->latest()
            ->get();

        $totalKelas = $assignments->pluck('class_id')->unique()->count();
        $totalMapel = $assignments->pluck('subject_name')->unique()->count();

        $jadwalMendatang = Schedule::where('teacher_id', $teacherId)
            ->whereDate('date', '>=', $today->toDateString())
            ->with('class')
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(8)
            ->get();

        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
            ->whereDate('date', $today->toDateString())
            ->count();

        $jadwalTutor = DedicatedTutor::where('teacher_id', $teacherId)
            ->where('status', 'confirmed')
            ->whereDate('date', '>=', $today->toDateString())
            ->with(['student.user', 'material'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->take(8)
            ->get();

        $tutorHariIni = DedicatedTutor::where('teacher_id', $teacherId)
            ->where('status', 'confirmed')
            ->whereDate('date', $today->toDateString())
            ->count();

        $materiTerbaru = collect();
        $totalMateri = 0;

        if (Schema::hasTable('materials') && Schema::hasColumn('materials', 'user_id')) {
            $totalMateri = Material::where('user_id', $teacherId)->count();

            $materiTerbaru = Material::where('user_id', $teacherId)
                ->with('class')
                ->latest()
                ->take(5)
                ->get();
        }

        $totalLatihan = $this->safeTeacherCount(
            PracticeQuestion::class,
            'practice_questions',
            $teacherId
        );

        $totalTryout = $this->safeTeacherCount(
            TryoutSubmission::class,
            'tryout_submissions',
            $teacherId
        );

        $aktivitasTerbaru = collect();

        foreach ($materiTerbaru->take(3) as $materi) {
            $aktivitasTerbaru->push([
                'type' => 'Materi',
                'icon' => 'fa-book-open',
                'title' => $materi->title ?? $materi->material_name ?? 'Materi baru',
                'subtitle' => 'Materi berhasil diunggah',
                'time' => $materi->created_at,
                'sort_time' => optional($materi->created_at)->timestamp ?? 0,
            ]);
        }

        foreach ($jadwalMendatang->take(3) as $jadwal) {
            $aktivitasTerbaru->push([
                'type' => 'Jadwal',
                'icon' => 'fa-calendar-days',
                'title' => $jadwal->title,
                'subtitle' => ($jadwal->class->program_name ?? 'Program') . ' • ' . Carbon::parse($jadwal->date)->translatedFormat('d M Y'),
                'time' => $jadwal->created_at,
                'sort_time' => Carbon::parse($jadwal->date . ' ' . $jadwal->start_time)->timestamp,
            ]);
        }

        foreach ($jadwalTutor->take(3) as $tutor) {
            $aktivitasTerbaru->push([
                'type' => 'Tutor',
                'icon' => 'fa-headset',
                'title' => $tutor->student->user->name ?? 'Siswa',
                'subtitle' => 'Sesi dedicated tutor terkonfirmasi',
                'time' => $tutor->created_at,
                'sort_time' => Carbon::parse($tutor->date . ' ' . $tutor->time)->timestamp,
            ]);
        }

        $aktivitasTerbaru = $aktivitasTerbaru
            ->sortByDesc('sort_time')
            ->take(6)
            ->values();

        return view('pengajar.dashboard', compact(
            'assignments',
            'totalKelas',
            'totalMapel',
            'totalMateri',
            'totalLatihan',
            'totalTryout',
            'jadwalMendatang',
            'jadwalHariIni',
            'jadwalTutor',
            'tutorHariIni',
            'materiTerbaru',
            'aktivitasTerbaru'
        ));
    }

    private function safeTeacherCount(string $modelClass, string $tableName, int $teacherId): int
    {
        if (!class_exists($modelClass) || !Schema::hasTable($tableName)) {
            return 0;
        }

        if (Schema::hasColumn($tableName, 'user_id')) {
            return $modelClass::where('user_id', $teacherId)->count();
        }

        if (Schema::hasColumn($tableName, 'teacher_id')) {
            return $modelClass::where('teacher_id', $teacherId)->count();
        }

        return 0;
    }
}