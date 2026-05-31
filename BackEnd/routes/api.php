<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Api\pengajar\DedicatedTutorController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TryoutController; 
use App\Models\{Announcement, Material, ClassModel, TryoutResult};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Route, DB, Http};
use App\Http\Controllers\Api\BannerController;

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Gateway Mode)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()->json(['status' => 'success', 'message' => 'Specta Academy API is Ready']);
});

// --- PUBLIC ROUTES ---
Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Midtrans Webhook
Route::post('/midtrans-callback', [PaymentController::class, 'handleNotification']);

// --- PROTECTED ROUTES (Wajib Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {

    // PROFILE & BANNERS
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load(['role', 'student.class']);
        $activeEnrollment = DB::table('enrollments')->where('user_id', $user->usersID)->where('status', 'active')->first();
        if ($activeEnrollment) {
            if ($user->student) $user->student->class_id = $activeEnrollment->class_id;
            $user->active_class_id = $activeEnrollment->class_id;
        }
        return response()->json($user);
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/promos', [PromoController::class, 'apiIndex']);

    // ============================================================
    // 🆕 TRYOUT SYSTEM (GATEWAY TO GO SERVICE 9003)
    // ============================================================
    Route::prefix('tryouts')->group(function () {
        Route::get('/', [TryoutController::class, 'index']);           // List Paket
        Route::get('/my', [TryoutController::class, 'history']);       // Riwayat Nilai
        Route::get('/{id}/questions', [TryoutController::class, 'questions']); // Ambil Soal
        Route::post('/{id}/submit', [TryoutController::class, 'submit']);      // Submit Skor
    });

    // --- STUDENT SPECIFIC ---
    Route::middleware('role:siswa')->group(function () {
        Route::get('/learning-report', function(Request $request) {
            $data = TryoutResult::where('user_id', $request->user()->usersID)->latest()->take(7)->get()->reverse()->values();
            return response()->json(['status' => 'success', 'data' => $data]);
        });
        Route::get('/schedules', [AuthController::class, 'getSiswaSchedule']);
        Route::get('/materials', function (Request $request) {
            return response()->json(['status' => 'success', 'data' => Material::where('class_id', $request->class_id)->orderBy('week', 'asc')->get()]);
        });
        Route::get('/tutor/history', [DedicatedTutorController::class, 'index']);
        Route::post('/tutor/submit', [DedicatedTutorController::class, 'store']);
    });

    Route::post('/payment/snap-token', [PaymentController::class, 'getSnapToken']);
    Route::post('/promo/check', [PromoController::class, 'checkPromo']);
});

// Gateway Content (Materi List)
Route::match(['get', 'post'], '/class/content', [AuthController::class, 'getClassContent']);