<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ClassModel;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajarDashboardController extends Controller
{
    public function index(): View
    {
        return view('pengajar.dashboard');
    }

    public function absensi(): View
    {
        $classes = ClassModel::all();
        $teacherId = Auth::user()->usersID; // Tetap usersID sesuai migrasi User Anda
        $today = date('Y-m-d');

        // Ambil daftar class_id yang ada jadwalnya hari ini
        $jadwalHariIni = Schedule::where('teacher_id', $teacherId)
                            ->whereDate('date', $today)
                            ->pluck('class_id')
                            ->toArray();

        return view('pengajar.absensi.index', compact('classes', 'jadwalHariIni'));
    }

    public function showAbsensi($class_id): View | \Illuminate\Http\RedirectResponse
        {
            $isAssigned = Schedule::where('class_id', $class_id)
                                ->where('teacher_id', Auth::user()->usersID)
                                ->where('date', date('Y-m-d'))
                                ->first();

            if (!$isAssigned) {
                return redirect()->route('pengajar.absensi.index')->with('info', 'No schedule for this class today.');
            }

            // Cek apakah sudah ada data absen untuk hari ini
            // PERBAIKAN: Gunakan schedule_id sesuai PK baru
            $hasAttendance = Attendance::where('schedule_id', $isAssigned->schedule_id)
                                    ->where('date', date('Y-m-d'))
                                    ->exists();

            $siswas = Enrollment::where('class_id', $class_id)
                        ->where('status', 'active') // Pastikan status adalah 'active'
                        ->where('expires_at', '>', now())
                        ->with(['user.student'])
                        ->get(); // PERBAIKAN: Gunakan -> bukan .

            return view('pengajar.absensi.show', compact('siswas', 'isAssigned', 'hasAttendance'));
        }

    public function storeAbsensi(Request $request)
        {
            // 1. Validasi: Jika tidak ada status yang dikirim (tabel kosong), gagalkan proses
            if (!$request->has('status')) {
                return back()->with('error', 'No student data found to save.');
            }

            // 2. Proses Simpan
            foreach ($request->status as $usersID => $status) {
                Attendance::updateOrCreate(
                    [
                        'schedule_id' => $request->schedule_id,
                        'user_id'     => $usersID,
                        'date'        => date('Y-m-d')
                    ],
                    ['status' => $status]
                );
            }

            $schedule = Schedule::find($request->schedule_id);
            return redirect()->route('pengajar.absensi.show', $schedule->class_id)
                            ->with('success', 'Attendance saved successfully!');
        }

    public function detailAbsensi($schedule_id): View
    {
        $schedule = Schedule::findOrFail($schedule_id);
        $attendances = Attendance::where('schedule_id', $schedule_id)
                                 ->where('date', date('Y-m-d'))
                                 ->with('user')
                                 ->get();

        return view('pengajar.absensi.detail', compact('attendances', 'schedule'));
    }

    public function jadwalSaya(): View
    {
        $jadwal = Schedule::where('teacher_id', Auth::user()->usersID)
                    ->with('class')
                    ->orderBy('date', 'asc')
                    ->get();

        return view('pengajar.jadwal.index', compact('jadwal'));
    }
}
