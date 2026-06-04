<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\DedicatedTutor;
use App\Models\Material;
use App\Models\PracticeQuestion;
use App\Models\Schedule;
use App\Models\TeacherAssignment;
use App\Models\TryoutSubmission;
use App\Models\ClassModel;
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

        // MODIFIKASI: Tambahkan eager loading 'subject' agar totalMapel bisa dihitung
        $assignments = TeacherAssignment::with(['classModel', 'subject'])
            ->where('user_id', $teacherId)
            ->latest()
            ->get();

        $totalKelas = $assignments->pluck('class_id')->unique()->count();

        // MODIFIKASI: Ambil dari relasi subject (tabel materials)
        $totalMapel = $assignments->pluck('subject.material_name')->unique()->count();

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

        if (Schema::connection('pgsql_materi')->hasTable('materials')) {
            $totalMateri = Material::where('user_id', $teacherId)->count();

            $materiTerbaru = Material::where('user_id', $teacherId)
                ->latest()
                ->take(5)
                ->get();

            $classIds = $materiTerbaru->pluck('class_id')->unique();
            $classes = ClassModel::whereIn('class_id', $classIds)->get()->keyBy('class_id');

            foreach ($materiTerbaru as $materi) {
                $materi->setRelation('class', $classes->get($materi->class_id));
            }
        }

        $totalLatihan = $this->safeTeacherCount(PracticeQuestion::class, 'practice_questions', 'pgsql_practice', $teacherId);
        $totalTryout = $this->safeTeacherCount(TryoutSubmission::class, 'tryout_submissions', 'pgsql_tryout', $teacherId);

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
                // MODIFIKASI: Gunakan toDateString() untuk mencegah double time specification
                'sort_time' => Carbon::parse(Carbon::parse($jadwal->date)->toDateString() . ' ' . $jadwal->start_time)->timestamp,
            ]);
        }

        foreach ($jadwalTutor->take(3) as $tutor) {
            $aktivitasTerbaru->push([
                'type' => 'Tutor',
                'icon' => 'fa-headset',
                'title' => $tutor->student->user->name ?? 'Siswa',
                'subtitle' => 'Sesi dedicated tutor terkonfirmasi',
                'time' => $tutor->created_at,
                // MODIFIKASI: Gunakan toDateString() untuk mencegah double time specification
                'sort_time' => Carbon::parse(Carbon::parse($tutor->date)->toDateString() . ' ' . $tutor->time)->timestamp,
            ]);
        }

        $aktivitasTerbaru = $aktivitasTerbaru
            ->sortByDesc('sort_time')
            ->take(6)
            ->values();

        return view('pengajar.dashboard', compact(
            'assignments', 'totalKelas', 'totalMapel', 'totalMateri', 'totalLatihan', 'totalTryout',
            'jadwalMendatang', 'jadwalHariIni', 'jadwalTutor', 'tutorHariIni', 'materiTerbaru', 'aktivitasTerbaru'
        ));
    }

    private function safeTeacherCount(string $modelClass, string $tableName, string $connection, int $teacherId): int
    {
        if (!class_exists($modelClass) || !Schema::connection($connection)->hasTable($tableName)) {
            return 0;
        }

        if (Schema::connection($connection)->hasColumn($tableName, 'user_id')) {
            return $modelClass::where('user_id', $teacherId)->count();
        }

        if (Schema::connection($connection)->hasColumn($tableName, 'teacher_id')) {
            return $modelClass::where('teacher_id', $teacherId)->count();
        }

        return 0;
    }
}
