<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Api\pengajar\DedicatedTutorController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TryoutController; 
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\QuestionBankController; // ✅ IMPORT BERHASIL
use App\Models\{Announcement, Material, ClassModel, TryoutResult};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Route, DB, Http};

/*
|--------------------------------------------------------------------------
| API Routes - Specta Academy (Gateway & Auth System)
|--------------------------------------------------------------------------
*/

// --- 0. HANDSHAKE (Cek API Aktif) ---
Route::get('/', function () {
    return response()->json(['status' => 'success', 'message' => 'Specta Academy API is Ready']);
});

// --- 1. PUBLIC ROUTES (Tanpa Login) ---
Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Webhook Midtrans (Dipanggil otomatis oleh Midtrans)
Route::post('/midtrans-callback', [PaymentController::class, 'handleNotification']);

// --- 2. PROTECTED ROUTES (Wajib Bearer Token / Login) ---
Route::middleware('auth:sanctum')->group(function () {

    // ✅ PROFILE & GLOBAL DATA
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load(['role', 'student.class']);
        $activeEnrollment = DB::table('enrollments')
            ->where('user_id', $user->usersID)
            ->where('status', 'active')
            ->first();
            
        if ($activeEnrollment) {
            if ($user->student) $user->student->class_id = $activeEnrollment->class_id;
            $user->active_class_id = $activeEnrollment->class_id;
        }
        return response()->json($user);
    });

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/promos', [PromoController::class, 'apiIndex']);
    Route::get('/announcements', function() {
        return response()->json(['status' => 'success', 'data' => Announcement::latest()->get()]);
    });

    // ============================================================
    // ✅ QUESTION BANK HUB (Fitur Berbagi Soal Siswa)
    // ============================================================
    Route::prefix('question-bank')->group(function () {
        Route::get('/', [QuestionBankController::class, 'index']);       
        Route::post('/upload', [QuestionBankController::class, 'store']); 
    });

    // ✅ NOTIFICATION SYSTEM
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);                    
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);   
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']); 
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);      
    });

    // ✅ KATALOG KELAS
    Route::get('/classes', function () {
        $classes = ClassModel::all()->map(function ($item) {
            $item->price = (int) $item->price;
            $item->class_id = (int) $item->class_id;
            return $item;
        });
        return response()->json(['status' => 'success', 'data' => $classes]);
    });

    // ✅ JADWAL SYSTEM
    Route::prefix('schedules')->group(function () {
        Route::get('/today', [ScheduleController::class, 'today']); 
        Route::get('/all', [ScheduleController::class, 'index']);   
    });

    // ✅ TRYOUT SYSTEM
    Route::prefix('tryouts')->group(function () {
        Route::get('/', [TryoutController::class, 'index']);           
        Route::get('/history', [TryoutController::class, 'history']);  
        Route::get('/my', [TryoutController::class, 'history']);       
        Route::get('/questions', [TryoutController::class, 'questions']); 
        Route::get('/{id}/questions', [TryoutController::class, 'questions']); 
        Route::post('/{id}/submit', [TryoutController::class, 'submit']);      
        Route::get('/results/{id}', [TryoutController::class, 'results']);
    });

    // ✅ KHUSUS ROLE SISWA
    Route::middleware('role:siswa')->group(function () {
        
        // Report Grafik Belajar
        Route::get('/learning-report', function(Request $request) {
            $data = TryoutResult::where('user_id', $request->user()->usersID)
                ->latest()
                ->take(7)
                ->get()
                ->reverse()
                ->values();
            return response()->json(['status' => 'success', 'data' => $data]);
        });

        // Materi Belajar berdasarkan Kelas
        Route::get('/materials', function (Request $request) {
            return response()->json([
                'status' => 'success', 
                'data' => Material::where('class_id', $request->class_id)->orderBy('week', 'asc')->get()
            ]);
        });
        
        Route::post('/class/content', [AuthController::class, 'getClassContent']);

        // ============================================================
        // ✅ DEDICATED TUTOR (Request & Sisa Kuota)
        // ============================================================
        // Endpoint untuk mengambil Riwayat, Daftar Topik, dan Info Sisa Kuota
        Route::get('/tutor/history', [DedicatedTutorController::class, 'index']); 
        
        // Endpoint untuk mengirim Request Sesi Tutor Baru
        Route::post('/tutor/submit', [DedicatedTutorController::class, 'store']); 
    });

    // ✅ UTILITY ROUTES (Payment, Promo, Logout)
    Route::post('/payment/snap-token', [PaymentController::class, 'getSnapToken']);
    Route::post('/promo/check', [PromoController::class, 'checkPromo']);
    Route::post('/logout', [AuthController::class, 'logout']);
});