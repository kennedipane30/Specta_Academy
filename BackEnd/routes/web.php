<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

// --- IMPORT CONTROLLERS ---
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\{
    AdminDashboardController,
    PembayaranController,
    ManajemenSiswaController,
    JadwalController,
    ManajemenPengajarController,
    AdminDedicatedTutorController,
    TeacherAssignmentController,
    PromoController,
    AnnouncementController,
    ClassManagementController,
    BannerController,
    AdminTryoutController
};
use App\Http\Controllers\Pengajar\{
    PengajarDashboardController,
    MateriController,
    TryoutController as PengajarTryoutController,
    AbsensiController,
    PracticeQuestionController
};

// --- ROOT REDIRECT ---
Route::get('/', function () { return redirect()->route('login'); });

// --- 1. AUTHENTICATION (WEB) ---
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// ============================================================
// 🟢 ROUTE GLOBAL (Auth Required)
// ============================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/my-schedule', [JadwalController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/calendar-data', [JadwalController::class, 'getCalendarData'])->name('schedule.calendar');
});

// ============================================================
// 🔥 2. GROUP ADMIN (Role: Admin)
// ============================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // ✅ MONITORING NILAI SISWA
    Route::prefix('scores')->name('scores.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'pilihKelas'])->name('index');
        Route::get('/class/{class_id}', [AdminTryoutController::class, 'pilihTryout'])->name('pilih_tryout');
        Route::get('/result/{tryout_id}', [AdminTryoutController::class, 'lihatNilai'])->name('result');
    });

    // ✅ MANAJEMEN GURU & JADWAL
    Route::resource('jadwal', JadwalController::class);
    Route::get('/jadwal/get-subjects/{class_id}', [JadwalController::class, 'getSubjects'])->name('jadwal.getSubjects');
    Route::get('/jadwal/get-teacher/{class_id}/{subject_id}', [JadwalController::class, 'getTeacherBySubject'])->name('jadwal.getTeacherBySubject');
    Route::resource('manajemen-pengajar', ManajemenPengajarController::class);

    // ✅ MANAJEMEN SISWA (AKTIVASI & PENDAFTARAN)
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/semua', [ManajemenSiswaController::class, 'index'])->name('index');
        Route::get('/tambah-kelas', [ManajemenSiswaController::class, 'indexPendaftaran'])->name('pendaftaran');
        Route::get('/tambah-kelas/aktivasi/{id}', [ManajemenSiswaController::class, 'formAktivasi'])->name('form_aktivasi');
        Route::post('/tambah-kelas/proses/{id}', [ManajemenSiswaController::class, 'prosesAktivasi'])->name('proses_aktivasi');
    });

    // ✅ KEUANGAN & PROMO
    // Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    // Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');
    Route::resource('promo', PromoController::class)->only(['index', 'store', 'destroy']);

    // ✅ DEDICATED TUTOR (MANAJEMEN REQUEST SISWA)
    Route::get('/dedicated-tutor', [AdminDedicatedTutorController::class, 'index'])->name('tutor.index');
    Route::post('/dedicated-tutor/update/{id}', [AdminDedicatedTutorController::class, 'updateAssignment'])->name('tutor.update');

    // ✅ KURIKULUM, ANNOUNCEMENT & PENUGASAN MATERI
    Route::resource('announcement', AnnouncementController::class);
    Route::get('/penugasan-materi', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/penugasan-materi', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/penugasan-materi/{id}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

    // ✅ ROUTE AJAX UNTUK DROP DOWN (PENTING)
    Route::get('/get-subjects-by-class/{class_id}', [TeacherAssignmentController::class, 'getSubjectsByClass'])->name('getSubjectsByClass');

    // ✅ MASTER TRYOUT (KELOLA PAKET)
    Route::prefix('tryout-master')->name('tryout.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'index'])->name('index');
        Route::get('/review/{class_id}', [AdminTryoutController::class, 'reviewDrafts'])->name('review');
        Route::get('/export-draft/{class_id}', [AdminTryoutController::class, 'exportDraftCsv'])->name('export_draft');
        Route::post('/publish', [AdminTryoutController::class, 'publishToMobile'])->name('publish');
        Route::delete('/draft/destroy/{id}', [AdminTryoutController::class, 'deleteDraft'])->name('draft.delete');
        Route::delete('/package/destroy/{tryout_id}', [AdminTryoutController::class, 'destroyPackage'])->name('destroy_package');
    });

    // ✅ SETTINGS GLOBAL (CLASSES & BANNERS)
    Route::resource('classes', ClassManagementController::class);
    Route::resource('banners', BannerController::class)->except(['show']);
});

// ============================================================
// 🔥 3. GROUP PENGAJAR (Role: Pengajar) - DIPERBAIKI
// ============================================================
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {

    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');

    // ✅ MANAJEMEN MATERI BELAJAR
    Route::prefix('materi')->name('materi.')->group(function() {
        Route::get('/', [MateriController::class, 'index'])->name('index');
        Route::get('/pilih/{class_id}/{subject_name}', [MateriController::class, 'pilihMateri'])->name('pilih');
        Route::post('/upload/{class_id}', [MateriController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [MateriController::class, 'destroy'])->name('destroy');
    });

    // ✅ TRYOUT SYSTEM (SETOR SOAL)
    Route::prefix('tryout')->name('tryout.')->group(function() {
        Route::get('/', [PengajarTryoutController::class, 'index'])->name('index');
        Route::get('/buat/{class_id}/{subject_name}', [PengajarTryoutController::class, 'create'])->name('create');
        Route::post('/simpan', [PengajarTryoutController::class, 'store'])->name('store');
        Route::post('/import-csv', [PengajarTryoutController::class, 'importCSV'])->name('import_csv');
        Route::delete('/draft/delete/{id}', [PengajarTryoutController::class, 'destroyDraft'])->name('destroy_draft');
        Route::post('/delete-all', [PengajarTryoutController::class, 'deleteAllDrafts'])->name('deleteAll');
    });

    // ✅ ABSENSI SISWA
 Route::prefix('absensi')->name('absensi.')->group(function() {
    Route::get('/', [AbsensiController::class, 'index'])->name('index');
    Route::get('/weeks/{class_id}/{subject}', [AbsensiController::class, 'listWeeks'])->name('weeks');
    Route::get('/create/{class_id}/{subject}/{week}', [AbsensiController::class, 'create'])->name('create');
    Route::post('/store', [AbsensiController::class, 'store'])->name('store');
    Route::get('/recap/{class_id}/{subject}/{week}', [AbsensiController::class, 'showRecap'])->name('recap');

    // 🔹 TAMBAHKAN 4 ROUTE INI:
    Route::get('/edit/{class_id}/{subject}/{week}', [AbsensiController::class, 'edit'])->name('edit');
    Route::put('/update/{class_id}/{subject}/{week}', [AbsensiController::class, 'update'])->name('update');
    Route::delete('/destroy/{class_id}/{subject}/{week}', [AbsensiController::class, 'destroy'])->name('destroy');
    Route::get('/export-pdf/{class_id}/{subject}/{week}', [AbsensiController::class, 'exportPdf'])->name('export-pdf');
});

    // ✅ MANAJEMEN LATIHAN SOAL (CSV) - DIPERBAIKI
    Route::prefix('latihan')->name('latihan.')->group(function() {
        Route::get('/', [PracticeQuestionController::class, 'index'])->name('index');
        // Select practice - menggunakan subject_name
        Route::get('/pilih/{class_id}/{subject_name}', [PracticeQuestionController::class, 'selectPractice'])->name('pilih');
        // Upload CSV
        Route::post('/upload/{class_id}', [PracticeQuestionController::class, 'storeCSV'])->name('store');
        // Delete week - KONSISTEN menggunakan subject_name
        Route::delete('/destroy-week/{class_id}/{subject_name}/{week}', [PracticeQuestionController::class, 'destroyByWeek'])->name('destroy_week');
        // Lihat soal per minggu
        Route::get('/questions/{class_id}/{subject_name}/{week}', [PracticeQuestionController::class, 'showQuestions'])->name('questions');
    });
});

// ============================================================
// 🟢 4. STABLE FILE SERVER (PDF BYPASS)
// ============================================================
Route::get('/storage/materi/{filename}', function ($filename) {
    $path = storage_path('app/public/materi/' . basename($filename));
    if (!File::exists($path)) abort(404);
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
})->name('storage.materi.bypass');
