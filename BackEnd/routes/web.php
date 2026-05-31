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

Route::get('/', function () { return redirect()->route('login'); });

// --- AUTHENTICATION ---
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// ============================
// 🔥 1. GROUP ADMIN (Role: Admin)
// ============================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Monitoring Nilai Siswa
    Route::prefix('scores')->name('scores.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'pilihKelas'])->name('index');
        Route::get('/class/{class_id}', [AdminTryoutController::class, 'pilihTryout'])->name('pilih_tryout');
        Route::get('/result/{tryout_id}', [AdminTryoutController::class, 'lihatNilai'])->name('result');
    });

    // Manajemen Guru & Jadwal
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

    // Keuangan & Promo
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');
    Route::resource('promo', PromoController::class)->only(['index', 'store', 'destroy']);

    // Penugasan Kurikulum & Pengumuman
    Route::get('/dedicated-tutor', [AdminDedicatedTutorController::class, 'index'])->name('tutor.index');
    Route::post('/dedicated-tutor/update/{id}', [AdminDedicatedTutorController::class, 'updateAssignment'])->name('tutor.update');
    Route::resource('announcement', AnnouncementController::class);
    Route::get('/penugasan-materi', [TeacherAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('/penugasan-materi', [TeacherAssignmentController::class, 'store'])->name('assignments.store');
    Route::delete('/penugasan-materi/{id}', [TeacherAssignmentController::class, 'destroy'])->name('assignments.destroy');

    /**
     * ✨ MASTER TRYOUT ADMIN (Kurasi & Publish)
     * Bagian ini telah diperbaiki rute Publish-nya
     */
    Route::prefix('tryout-master')->name('tryout.')->group(function() {
        Route::get('/', [AdminTryoutController::class, 'index'])->name('index');
        Route::get('/review/{class_id}', [AdminTryoutController::class, 'reviewDrafts'])->name('review');
        Route::get('/export-draft/{class_id}', [AdminTryoutController::class, 'exportDraftCsv'])->name('export_draft');
        
        // MODIFIKASI: Menghapus {class_id} karena id dikirim via input hidden di form POST
        Route::post('/publish', [AdminTryoutController::class, 'publishToMobile'])->name('publish');
        
        Route::delete('/draft/destroy/{id}', [AdminTryoutController::class, 'deleteDraft'])->name('draft.delete');
        Route::delete('/package/destroy/{tryout_id}', [AdminTryoutController::class, 'destroyPackage'])->name('destroy_package');
    });

    Route::resource('classes', ClassManagementController::class);
    Route::resource('banners', BannerController::class)->except(['show']);
});

// ============================
// 🔥 2. GROUP PENGAJAR (Role: Pengajar)
// ============================
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {
    
    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');

    // Materi
    Route::prefix('materi')->name('materi.')->group(function() {
        Route::get('/', [MateriController::class, 'index'])->name('index');
        Route::get('/pilih/{class_id}/{subject_name}', [MateriController::class, 'pilihMateri'])->name('pilih');
        Route::post('/upload/{class_id}', [MateriController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [MateriController::class, 'destroy'])->name('destroy');
    });

    /**
     * ✨ TRYOUT PENGAJAR (Setor Soal)
     */
    Route::prefix('tryout')->name('tryout.')->group(function() {
        Route::get('/', [PengajarTryoutController::class, 'index'])->name('index');
        Route::get('/buat/{class_id}/{subject_name}', [PengajarTryoutController::class, 'create'])->name('create');
        Route::post('/simpan', [PengajarTryoutController::class, 'store'])->name('store');
        Route::post('/import-csv', [PengajarTryoutController::class, 'importCSV'])->name('import_csv');
        Route::delete('/draft/delete/{id}', [PengajarTryoutController::class, 'destroyDraft'])->name('destroy_draft');
    });

    Route::resource('absensi', AbsensiController::class);
    Route::prefix('latihan')->name('latihan.')->group(function() {
        Route::get('/', [PracticeQuestionController::class, 'index'])->name('index');
        Route::get('/pilih/{class_id}/{subject_name}', [PracticeQuestionController::class, 'selectPractice'])->name('pilih');
        Route::post('/upload/{class_id}', [PracticeQuestionController::class, 'storeCSV'])->name('store');
        Route::delete('/destroy-week/{class_id}/{subject}/{week}', [PracticeQuestionController::class, 'destroyByWeek'])->name('destroy_week');
    });
});

// --- STABLE FILE SERVER ---
Route::get('/storage/materi/{filename}', function ($filename) {
    $path = storage_path('app/public/materi/' . basename($filename));
    if (!File::exists($path)) abort(404);
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
    ]);
})->name('storage.materi.bypass');