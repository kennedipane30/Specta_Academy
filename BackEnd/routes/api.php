<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\GaleriController;
use App\Http\Controllers\Admin\PromoController;
// PERBAIKAN: Import diarahkan ke namespace folder Api/pengajar agar tidak error
use App\Http\Controllers\Api\pengajar\DedicatedTutorController;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Material;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Mobile)
|--------------------------------------------------------------------------
*/

// --- 1. PUBLIC ROUTES ---
Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/galeri', [GaleriController::class, 'apiIndex']);
Route::get('/promos', [PromoController::class, 'apiIndex']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/announcements', function() {
    return response()->json([
        'status' => 'success',
        'data' => Announcement::latest()->get()
    ]);
});

// --- 2. PROTECTED ROUTES (Wajib bawa Token / auth:sanctum) ---
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user-profile', function (Request $request) {
        return $request->user()->load(['role', 'student.class_model']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user()->load(['role', 'student.class_model']);
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- 3. KHUSUS ROLE SISWA ---
    Route::middleware('role:siswa')->group(function () {

        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);
        Route::post('/promo/check', [AuthController::class, 'checkPromo']);
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);
        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);
        Route::post('/class/join-promo', [AuthController::class, 'joinClassPromo']);

        Route::get('/materials', function (Request $request) {
            $classId = $request->query('class_id');
            $data = Material::where('class_id', $classId)->get();
            return response()->json(['data' => $data]);
        });

        // --- TAMBAHAN FITUR DEDICATED TUTOR (MODIFIKASI SINKRON) ---
        // 1. Mengambil materi sesuai kelas siswa untuk dropdown
        Route::get('/tutor/form-data', [DedicatedTutorController::class, 'getTutorFormData']);

        // 2. Mengambil riwayat pengajuan (History)
        Route::get('/dedicated-tutors', [DedicatedTutorController::class, 'index']);

        // 3. Menyimpan pengajuan baru
        Route::post('/dedicated-tutors', [DedicatedTutorController::class, 'store']);
    });
});
