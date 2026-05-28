<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

// --- IMPORT AUTH CONTROLLER ---
use App\Http\Controllers\WebAuthController;

// --- IMPORT ADMIN CONTROLLERS ---
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

// --- IMPORT PENGAJAR CONTROLLERS ---
use App\Http\Controllers\Pengajar\PengajarDashboardController;
use App\Http\Controllers\Pengajar\MateriController;
use App\Http\Controllers\Pengajar\TryoutController;
use App\Http\Controllers\Pengajar\AbsensiController;
use App\Http\Controllers\Pengajar\PracticeQuestionController;

/*
|--------------------------------------------------------------------------
| Web Routes - Specta Academy
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return redirect()->route('login'); });

// --- AUTHENTICATION ---
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// ============================
// 🔥 1. GROUP ADMIN (Role: Admin)
// ============================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Fitur Rekap Nilai
    Route::prefix('scores')->name('scores.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'pilihKelas'])->name('index');
        Route::get('/class/{class_id}', [AdminTryoutController::class, 'pilihTryout'])->name('pilih_tryout');
        Route::get('/result/{tryout_id}', [AdminTryoutController::class, 'lihatNilai'])->name('result');
    });

    // Manajemen Jadwal & Akun Pengajar
    Route::resource('jadwal', JadwalController::class);
    Route::get('/get-materi/{class_id}', [JadwalController::class, 'getMateri'])->name('jadwal.getMateri');
    Route::resource('manajemen-pengajar', ManajemenPengajarController::class);

    // Manajemen Siswa
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/semua', [ManajemenSiswaController::class, 'index'])->name('index');
        Route::get('/tambah-kelas', [ManajemenSiswaController::class, 'indexPendaftaran'])->name('pendaftaran');
        Route::get('/tambah-kelas/aktivasi/{id}', [ManajemenSiswaController::class, 'formAktivasi'])->name('form_aktivasi');
        Route::post('/tambah-kelas/proses/{id}', [ManajemenSiswaController::class, 'prosesAktivasi'])->name('proses_aktivasi');
    });

    // Dedicated Tutor & Pengumuman
    Route::get('/dedicated-tutor', [AdminDedicatedTutorController::class, 'index'])->name('tutor.index');
    Route::post('/dedicated-tutor/update/{id}', [AdminDedicatedTutorController::class, 'updateAssignment'])->name('tutor.update');
    Route::resource('announcement', AnnouncementController::class);

    // Keuangan (Pembayaran)
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');

    // Manajemen Promo
    Route::get('/promo', [PromoController::class, 'index'])->name('promo.index');
    Route::post('/promo', [PromoController::class, 'store'])->name('promo.store');
    Route::delete('/promo/{id}', [PromoController::class, 'destroy'])->name('promo.destroy');

    // Penugasan Guru
    Route::get('/penugasan-materi', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/penugasan-materi', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/penugasan-materi/{id}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

    // Fitur Master Tryout
    Route::prefix('tryout-master')->name('tryout.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'index'])->name('index');
        Route::get('/export/{class_id}', [AdminTryoutController::class, 'exportCsv'])->name('export');
        Route::post('/upload-final', [AdminTryoutController::class, 'uploadMaster'])->name('upload');
        Route::delete('/tryout-destroy/{class_id}', [AdminTryoutController::class, 'destroyPackage'])->name('destroy_package');
    });

    // Manajemen Katalog Kelas
    Route::resource('classes', ClassManagementController::class)->only(['index', 'edit', 'update', 'create', 'store', 'destroy']);

    // Banner Promo
    Route::resource('banners', BannerController::class)->except(['show']);
});

// ============================
// 🔥 2. GROUP PENGAJAR (Role: Pengajar)
// ============================
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');

    // Absensi
    Route::prefix('absensi')->name('absensi.')->group(function() {
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::get('/weeks/{class_id}/{subject}', [AbsensiController::class, 'listWeeks'])->name('weeks');
        Route::get('/isi/{class_id}/{subject}/{week}', [AbsensiController::class, 'create'])->name('create');
        Route::post('/simpan', [AbsensiController::class, 'store'])->name('store');
        Route::get('/recap/{class_id}/{subject}/{week}', [AbsensiController::class, 'showRecap'])->name('recap');
    });

    // Materi
    Route::prefix('materi')->name('materi.')->group(function() {
        Route::get('/', [MateriController::class, 'index'])->name('index');
        Route::get('/pilih/{class_id}/{subject_name}', [MateriController::class, 'pilihMateri'])->name('pilih');
        Route::post('/upload/{class_id}', [MateriController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [MateriController::class, 'destroy'])->name('destroy');
    });

    // Tryout
    Route::prefix('tryout')->name('tryout.')->group(function() {
        Route::get('/', [TryoutController::class, 'index'])->name('index');
        Route::get('/buat/{class_id}/{subject_name}', [TryoutController::class, 'create'])->name('create');
        Route::post('/simpan', [TryoutController::class, 'store'])->name('store');
    });

    // Latihan
    Route::prefix('latihan')->name('latihan.')->group(function() {
        Route::get('/', [PracticeQuestionController::class, 'index'])->name('index');
        Route::get('/pilih/{class_id}/{subject_name}', [PracticeQuestionController::class, 'selectPractice'])->name('pilih');
        Route::post('/upload/{class_id}', [PracticeQuestionController::class, 'storeCSV'])->name('store');
        Route::delete('/destroy-week/{class_id}/{subject}/{week}', [PracticeQuestionController::class, 'destroyByWeek'])->name('destroy_week');
    });
});

// ============================
// 🔥 3. FILE SERVING - MODIFIKASI TOTAL UNTUK FIX CONNECTION CLOSED
// ============================

/**
 * ✅ ROUTE PDF MATERI - VERSI SUPER STABLE
 * Menggunakan readfile() native PHP + flush buffer
 * Ini adalah solusi paling kompatibel untuk php artisan serve
 */
Route::get('/storage/materi/{filename}', function ($filename) {
    // Sanitasi filename
    $filename = basename($filename);
    $path = storage_path('app/public/materi/' . $filename);

    // Cek file exist
    if (!File::exists($path)) {
        abort(404, 'File tidak ditemukan.');
    }

    // Bersihkan semua output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Set headers untuk PDF
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($path));
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Connection: keep-alive');
    
    // Kirim file langsung ke output
    readfile($path);
    exit;
})->name('storage.materi.bypass');

/**
 * ✅ ROUTE ALTERNATIF - VIA PUBLIC FOLDER (LEBIH CEPAT)
 * Copy file ke public/test.pdf untuk testing
 */
Route::get('/pdf-materi/{filename}', function ($filename) {
    $filename = basename($filename);
    $path = public_path('pdf-temp/' . $filename);
    
    // Jika file tidak ada di public, cari di storage
    if (!File::exists($path)) {
        $storagePath = storage_path('app/public/materi/' . $filename);
        if (File::exists($storagePath)) {
            // Buat folder temp jika belum ada
            if (!File::exists(public_path('pdf-temp'))) {
                File::makeDirectory(public_path('pdf-temp'), 0755, true);
            }
            // Copy ke public folder
            File::copy($storagePath, $path);
        } else {
            abort(404);
        }
    }
    
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Cache-Control' => 'no-cache',
    ]);
})->name('storage.materi.alternative');

/**
 * ✅ Route TEST - Cek status file PDF
 */
Route::get('/check-pdf/{filename}', function ($filename) {
    $filename = basename($filename);
    $path = storage_path('app/public/materi/' . $filename);
    
    if (!File::exists($path)) {
        return response()->json([
            'exists' => false,
            'message' => 'File not found'
        ]);
    }
    
    // Baca 4 byte pertama untuk validasi PDF
    $handle = fopen($path, 'rb');
    $header = fread($handle, 4);
    fclose($handle);
    
    return response()->json([
        'exists' => true,
        'filename' => $filename,
        'size' => File::size($path),
        'is_pdf' => ($header === '%PDF') ? true : false,
        'header' => bin2hex($header),
        'readable' => is_readable($path),
        'path' => $path,
    ]);
});

/**
 * ✅ Route generik untuk folder lain (gambar, dll)
 */
Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $folder   = basename($folder);
    $filename = basename($filename);
    $path     = storage_path("app/public/$folder/$filename");

    if (!File::exists($path)) {
        abort(404, 'File tidak ditemukan.');
    }

    return response()->file($path);
});

/**
 * ✅ Route galeri — tetap pakai response()->file() (gambar kecil)
 */
Route::get('/view-galeri/{filename}', function ($filename) {
    $filename = basename($filename);
    $path     = 'public/galeri/' . $filename;

    if (!Storage::exists($path)) {
        abort(404, 'File tidak ditemukan.');
    }

    return response()->file(storage_path('app/' . $path));
});