<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

// --- AUTH CONTROLLERS ---
use App\Http\Controllers\WebAuthController;

// --- ADMIN CONTROLLERS ---
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\ManajemenPengajarController;
use App\Http\Controllers\Admin\AdminDedicatedTutorController;
use App\Http\Controllers\Admin\TeacherAssignmentController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ClassManagementController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\AdminTryoutController;

// --- PENGAJAR CONTROLLERS ---
use App\Http\Controllers\Pengajar\PengajarDashboardController;
use App\Http\Controllers\Pengajar\MateriController;
use App\Http\Controllers\Pengajar\TryoutController;
use App\Http\Controllers\Pengajar\PracticeQuestionController;
use App\Http\Controllers\Pengajar\JadwalTutorController;

Route::get('/', function () { return redirect()->route('login'); });

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// ============================
// 🔥 1. GROUP ADMIN (Role: Admin)
// ============================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('scores')->name('scores.')->group(function() {
        Route::get('/', [TryoutController::class, 'lihatNilai'])->name('index');
        Route::get('/detail/{class_id}', [TryoutController::class, 'detailNilai'])->name('detail');
        Route::post('/export-selected', [TryoutController::class, 'exportPdfSelected'])->name('pdf_selected');
    });

    Route::resource('jadwal', JadwalController::class);
    Route::get('/get-materi/{class_id}', [JadwalController::class, 'getMateri'])->name('jadwal.getMateri');
    Route::resource('manajemen-pengajar', ManajemenPengajarController::class);

    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/semua', [ManajemenSiswaController::class, 'index'])->name('index');
        Route::get('/tambah-kelas', [ManajemenSiswaController::class, 'indexPendaftaran'])->name('pendaftaran');
        Route::get('/tambah-kelas/aktivasi/{id}', [ManajemenSiswaController::class, 'formAktivasi'])->name('form_aktivasi');
        Route::post('/tambah-kelas/proses/{id}', [ManajemenSiswaController::class, 'prosesAktivasi'])->name('proses_aktivasi');
    });

    Route::get('/dedicated-tutor', [AdminDedicatedTutorController::class, 'index'])->name('tutor.index');
    Route::post('/dedicated-tutor/update/{id}', [AdminDedicatedTutorController::class, 'updateAssignment'])->name('tutor.update');
    Route::resource('announcement', AnnouncementController::class);

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');

    Route::get('/promo', [PromoController::class, 'index'])->name('promo.index');
    Route::post('/promo', [PromoController::class, 'store'])->name('promo.store');
    Route::delete('/promo/{id}', [PromoController::class, 'destroy'])->name('promo.destroy');

    Route::get('/penugasan-materi', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/penugasan-materi', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/penugasan-materi/{id}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

    // FITUR MASTER TRYOUT (ADMIN)
    Route::prefix('tryout-master')->name('tryout.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'index'])->name('index');
        Route::get('/export/{class_id}', [AdminTryoutController::class, 'exportCsv'])->name('export');
        Route::post('/upload-final', [AdminTryoutController::class, 'uploadMaster'])->name('upload');
    });

    Route::resource('classes', ClassManagementController::class)->only(['index', 'edit', 'update','create','store', 'destroy']);
    Route::resource('banners', BannerController::class)->except(['show']);
});

// ============================
// 🔥 2. GROUP PENGAJAR (Role: Pengajar)
// ============================
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal-mengajar', [PengajarDashboardController::class, 'jadwalSaya'])->name('jadwal.index');
    Route::get('/absensi', [PengajarDashboardController::class, 'absensi'])->name('absensi.index');
    Route::get('/absensi/{class_id}', [PengajarDashboardController::class, 'showAbsensi'])->name('absensi.show');
    Route::post('/absensi/simpan', [PengajarDashboardController::class, 'storeAbsensi'])->name('absensi.store');
    Route::get('/absensi/detail/{schedule_id}', [PengajarDashboardController::class, 'detailAbsensi'])->name('absensi.detail');

    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/pilih/{class_id}/{subject_name}', [MateriController::class, 'pilihMateri'])->name('materi.pilih');
    Route::post('/materi/upload/{class_id}', [MateriController::class, 'store'])->name('materi.store');

    Route::prefix('tryout')->name('tryout.')->group(function() {
        Route::get('/', [TryoutController::class, 'index'])->name('index');
        Route::get('/buat/{class_id}/{subject_name}', [TryoutController::class, 'create'])->name('create');
        Route::post('/simpan', [TryoutController::class, 'store'])->name('store');
        Route::get('/nilai', [TryoutController::class, 'lihatNilai'])->name('nilai');
    });

    Route::prefix('latihan')->name('latihan.')->group(function() {
        Route::get('/', [PracticeQuestionController::class, 'index'])->name('index');
        Route::get('/pilih/{class_id}/{subject_name}', [PracticeQuestionController::class, 'selectPractice'])->name('pilih');
        Route::post('/upload/{class_id}', [PracticeQuestionController::class, 'storeCSV'])->name('store');
    });

    Route::get('/jadwal-tutor', [JadwalTutorController::class, 'index'])->name('tutor.index');
});

// View Gallery
Route::get('/view-galeri/{filename}', function ($filename) {
    $path = 'public/galeri/' . $filename;
    if (!Storage::exists($path)) abort(404);
    return Response::make(Storage::get($path), 200)->header("Content-Type", Storage::mimeType($path));
});
