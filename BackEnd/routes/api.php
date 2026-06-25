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

Route::get('/', function () {
    return response()->json(['status' => 'success', 'message' => 'Specta Academy API is Ready']);
});

Route::post('/register', [AuthController::class, 'registerSiswa']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/validate-reset-otp', [AuthController::class, 'validateResetOtp']);

Route::post('/midtrans-callback', [PaymentController::class, 'handleNotification']);

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

    Route::get('/profile', [AuthController::class, 'getProfile']);

    Route::post('/profile/photo', [AuthController::class, 'updatePhoto']);

    Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/promos', [PromoController::class, 'apiIndex']);
    Route::get('/announcements', function() {
        return response()->json(['status' => 'success', 'data' => Announcement::latest()->get()]);
    });


    Route::prefix('question-bank')->group(function () {
        Route::get('/', [QuestionBankController::class, 'index']);
        Route::post('/upload', [QuestionBankController::class, 'store']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    Route::get('/classes', function () {
        $classes = ClassModel::all()->map(function ($item) {
            $item->price = (int) $item->price;
            $item->class_id = (int) $item->class_id;
            return $item;
        });
        return response()->json(['status' => 'success', 'data' => $classes]);
    });

    Route::prefix('schedules')->group(function () {
        Route::get('/today', [ScheduleController::class, 'today']);
        Route::get('/all', [ScheduleController::class, 'index']);
    });


    Route::middleware('role:siswa')->group(function () {


        Route::get('/materials', function (Request $request) {
            try {
                $goMateriUrl = env('GO_MATERI_URL', 'http://127.0.0.1:9001');

                $response = Http::withHeaders([
                    'Authorization' => $request->header('Authorization'),
                    'Accept'        => 'application/json',
                ])->get("{$goMateriUrl}/api/materials", [
                    'class_id' => $request->class_id
                ]);

                return response()->json($response->json(), $response->status());
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal terhubung ke Layanan Materi Golang: ' . $e->getMessage()
                ], 500);
            }
        });

        Route::get('/practices', function (Request $request) {
            try {
                $goPracticeUrl = env('GO_PRACTICE_URL', 'http://127.0.0.1:9003');

                $queryParams = [];
                if ($request->has('class_id')) {
                    $queryParams['class_id'] = $request->class_id;
                }

                $response = Http::withHeaders([
                    'Authorization' => $request->header('Authorization'),
                    'Accept'        => 'application/json',
                ])->get("{$goPracticeUrl}/api/practices", $queryParams);

                return response()->json($response->json(), $response->status());
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal terhubung ke Layanan Latihan Soal Golang: ' . $e->getMessage()
                ], 500);
            }
        });

        Route::post('/class/content', [AuthController::class, 'getClassContent']);

        Route::get('/tutor/history', [DedicatedTutorController::class, 'index']);

        Route::post('/tutor/submit', [DedicatedTutorController::class, 'store']);
    });

    // ✅ UTILITY ROUTES (Payment, Promo, Logout)
    Route::post('/payment/snap-token', [PaymentController::class, 'getSnapToken']);
    Route::post('/promo/check', [PromoController::class, 'checkPromo']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/payment/manual-success', [PaymentController::class, 'manualPaymentSuccess']);

});
