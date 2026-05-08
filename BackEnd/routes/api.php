<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Api\pengajar\DedicatedTutorController;
use App\Http\Controllers\Api\QuestionBankController;
use App\Http\Controllers\Api\PaymentController;
use App\Models\Announcement;
use App\Models\Material;
use App\Models\ClassModel;
use App\Models\TryoutResult; // Tambahkan ini jika ingin menampilkan statistik nilai
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

// ✅ WEBHOOK MIDTRANS
Route::post('/midtrans-callback', [PaymentController::class, 'handleNotification']);
Route::post('/payment/callback', [PaymentController::class, 'handleNotification']);

// --- 2. PROTECTED ROUTES (Wajib Login / Pakai Token) ---
Route::middleware('auth:sanctum')->group(function () {

    // ✅ INFO AKADEMIK & PENGUMUMAN (Dinamis untuk Report Page)
    Route::get('/announcements', function() {
        return response()->json([
            'status' => 'success',
            'data' => Announcement::latest()->get() // Mengambil data terbaru dari Admin
        ]);
    });

    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/promos', [PromoController::class, 'apiIndex']);

    // ✅ PROMO & PAYMENT
    Route::post('/promo/check', [PromoController::class, 'checkPromo']);
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

        // ✅ STATISTIK BELAJAR (Untuk Grafik di Report Page)
        Route::get('/learning-report', function(Request $request) {
            $user = $request->user();
            // Mengambil 7 hasil tryout/latihan terakhir untuk grafik
            $stats = TryoutResult::where('user_id', $user->usersID)
                        ->latest()
                        ->take(7)
                        ->get()
                        ->reverse()
                        ->values();

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        });

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
