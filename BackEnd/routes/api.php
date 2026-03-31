<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\GaleriController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// --- JALUR LUPA PASSWORD ---
// 1. Kirim nomor WA untuk dapet OTP
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// 2. Kirim OTP dan Password Baru untuk Reset
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


// --- 2. PROTECTED ROUTES ---
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user()->load('role', 'student');
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);


    // --- 3. KHUSUS ROLE SISWA ---
    Route::middleware('role:siswa')->group(function () {

        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);

        // MODIFIKASI: Tambahkan rute untuk mengambil butir soal
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);

        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);
    });
});
