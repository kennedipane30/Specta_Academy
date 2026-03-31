<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Admin\ManajemenSiswaController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\ManajemenPengajarController;
use App\Http\Controllers\Admin\PromoController; // Import Controller Promo Admin
use App\Http\Controllers\Pengajar\PengajarDashboardController;
use App\Http\Controllers\Pengajar\MateriController;
use App\Http\Controllers\Pengajar\TryoutController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes - Spekta Academy
|--------------------------------------------------------------------------
*/

Route::get('/', function () { return redirect()->route('login'); });
Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');


// --- 1. GROUP ADMINISTRASI (Role: Admin) ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Galeri
    Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [GaleriController::class, 'store'])->name('galeri.store');
    Route::get('/galeri/edit/{id}', [GaleriController::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/update/{id}', [GaleriController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/hapus/{id}', [GaleriController::class, 'destroy'])->name('galeri.destroy');

    // Manajemen Jadwal & Akun Pengajar
    Route::resource('jadwal', JadwalController::class);
    Route::resource('manajemen-pengajar', ManajemenPengajarController::class);

    // Manajemen Siswa (Hierarkis)
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/semua', [ManajemenSiswaController::class, 'index'])->name('index');
        Route::get('/tambah-kelas', [ManajemenSiswaController::class, 'indexPendaftaran'])->name('pendaftaran');
        Route::get('/tambah-kelas/aktivasi/{id}', [ManajemenSiswaController::class, 'formAktivasi'])->name('form_aktivasi');
        Route::post('/tambah-kelas/proses/{id}', [ManajemenSiswaController::class, 'prosesAktivasi'])->name('proses_aktivasi');
    });

    // Keuangan & Verifikasi Pembayaran
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/verifikasi/{id}', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verify');

    // MODIFIKASI: Manajemen Promo (Hanya satu set rute yang aktif)
    Route::get('/promo', [PromoController::class, 'index'])->name('promo.index');
    Route::post('/promo', [PromoController::class, 'store'])->name('promo.store');
    Route::delete('/promo/{id}', [PromoController::class, 'destroy'])->name('promo.destroy');
});


// --- 2. GROUP PENGAJAR (Role: Pengajar) ---
Route::middleware(['auth', 'role:pengajar'])->prefix('pengajar')->name('pengajar.')->group(function () {

    Route::get('/dashboard', [PengajarDashboardController::class, 'index'])->name('dashboard');
    Route::get('/jadwal-mengajar', [PengajarDashboardController::class, 'jadwalSaya'])->name('jadwal.index');

    // Absensi Per Kelas
    Route::get('/absensi', [PengajarDashboardController::class, 'absensi'])->name('absensi.index');
    Route::get('/absensi/{class_id}', [PengajarDashboardController::class, 'showAbsensi'])->name('absensi.show');
    Route::post('/absensi/simpan', [PengajarDashboardController::class, 'storeAbsensi'])->name('absensi.store');

    // Materi
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/materi/pilih/{class_id}', [MateriController::class, 'pilihMateri'])->name('materi.pilih');
    Route::post('/materi/upload/{material_id}', [MateriController::class, 'store'])->name('materi.store');

    // Tryout
    Route::get('/soal-tryout', [TryoutController::class, 'buatSoal'])->name('tryout.create');
    Route::post('/soal-tryout/import', [TryoutController::class, 'importSoal'])->name('tryout.import');
    Route::get('/nilai', [TryoutController::class, 'lihatNilai'])->name('tryout.nilai');
});

// Jalur tampilan foto tanpa Symlink
Route::get('/view-galeri/{filename}', function ($filename) {
    $path = 'public/galeri/' . $filename;
    if (!Storage::exists($path)) abort(404);
    $file = Storage::get($path);
    $type = Storage::mimeType($path);
    return Response::make($file, 200)->header("Content-Type", $type);
});
