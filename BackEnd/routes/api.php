<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PromoController; // Pastikan namespace ini benar
use App\Http\Controllers\Api\pengajar\DedicatedTutorController;
use App\Http\Controllers\Api\QuestionBankController;
use App\Http\Controllers\Api\PaymentController; 
use App\Models\Announcement;
use App\Models\Material;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BannerController;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Mobile & Web)
|--------------------------------------------------------------------------
*/

// --- 0. SDK HANDSHAKE ---
Route::get('/', function () {
    return response()->json([
        'status' => 'success', 
        'message' => 'Specta Academy API is Ready'
    ]);
});

// --- 1. PUBLIC ROUTES (Tanpa Login) ---
Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// ✅ WEBHOOK MIDTRANS (Harus Publik / Di luar Middleware Auth)
Route::post('/midtrans-callback', [PaymentController::class, 'handleNotification']);
Route::post('/payment/callback', [PaymentController::class, 'handleNotification']);

// --- INFO PUBLIK ---
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/promos', [PromoController::class, 'apiIndex']); // Pastikan fungsi apiIndex ada di Controller
Route::get('/announcements', function() {
    return response()->json([
        'status' => 'success', 
        'data' => Announcement::latest()->get()
    ]);
});

// --- 2. PROTECTED ROUTES (Wajib Login / Pakai Token) ---
Route::middleware('auth:sanctum')->group(function () {

    // ✅ PROMO CHECK (Diarahkan ke PromoController fungsi checkPromo)
    Route::post('/promo/check', [PromoController::class, 'checkPromo']);

    // ✅ PAYMENT (Diarahkan ke PaymentController fungsi getSnapToken)
    Route::post('/payment/snap-token', [PaymentController::class, 'getSnapToken']);

    Route::get('/user', function (Request $request) {
        return $request->user()->load(['role', 'student.class']);
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/classes', function () {
        return response()->json([
            'status' => 'success',
            'data' => ClassModel::all()
        ]);
    });

    // --- KHUSUS SISWA ---
    Route::middleware('role:siswa')->group(function () {

        // CLASS & CONTENT
        Route::post('/class/content', [AuthController::class, 'getClassContent']);
        Route::post('/class/check-status', [AuthController::class, 'checkClassStatus']);
        Route::post('/class/join', [AuthController::class, 'joinClass']);
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);
        
        // TRYOUT & LATIHAN
        Route::post('/tryout/questions', [AuthController::class, 'getQuestions']);
        Route::post('/tryout/submit', [AuthController::class, 'submitTryout']);
        Route::get('/tryout/download/{id}', [AuthController::class, 'downloadPembahasan']);

        // MATERI
        Route::get('/materials', function (Request $request) {
            $classId = $request->query('class_id');
            if (!$classId) {
                return response()->json(['status' => 'error', 'message' => 'class_id diperlukan'], 400);
            }
            return response()->json([
                'status' => 'success',
                'data' => Material::where('class_id', $classId)->get()
            ]);
        });

        Route::get('/dedicated-tutors', [DedicatedTutorController::class, 'index']);
        Route::post('/dedicated-tutors', [DedicatedTutorController::class, 'store']);
    });
});