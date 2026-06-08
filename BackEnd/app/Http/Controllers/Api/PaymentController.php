<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * ✨ 1. GET SNAP TOKEN (PEMBUATAN TRANSAKSI)
     */
    public function getSnapToken(Request $request)
    {
        $request->validate([
            'class_id'   => 'required|exists:classes,class_id',
            'promo_code' => 'nullable|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Session expired. Please login again.'], 401);
        }

        // Get class data
        $class = ClassModel::find($request->class_id);
        if (!$class) {
            return response()->json(['message' => 'Program not found'], 404);
        }

        $basePrice  = (int) $class->price;
        $finalPrice = $basePrice;
        $appliedPromoCode = null;

        // PROMO LOGIC (FIXED & PERCENT)
        if ($request->filled('promo_code')) {
            $promoCode = strtoupper(trim($request->promo_code));
            $today = Carbon::today()->toDateString();

            $promo = DB::table('promotions')
                ->where('code', $promoCode)
                ->where('class_id', $class->class_id)
                ->where('is_active', 1)
                ->first();

            if ($promo) {
                $isValid = ($today >= $promo->start_date && $today <= $promo->end_date) && ($promo->quota > 0);
                if ($isValid) {
                    $discountAmount = ($promo->discount_type == 'percent')
                        ? ($basePrice * $promo->discount_percent) / 100
                        : (int) $promo->discount_percent;

                    $finalPrice = max(1000, $basePrice - $discountAmount);
                    $appliedPromoCode = $promoCode;
                }
            }
        }

        $orderId = 'ORDER-' . time() . '-' . $user->usersID;

        try {
            DB::beginTransaction();

            DB::table('payments')->insert([
                'user_id'    => $user->usersID,
                'class_id'   => $class->class_id,
                'order_id'   => $orderId,
                'amount'     => (int) $finalPrice,
                'promo_code' => $appliedPromoCode,
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => (int) $finalPrice,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'item_details' => [[
                    'id'       => $class->class_id,
                    'price'    => (int) $finalPrice,
                    'quantity' => 1,
                    'name'     => substr("Spekta: " . $class->program_name, 0, 45),
                ]],
            ];

            $transaction = Snap::createTransaction($params);
            DB::table('payments')->where('order_id', $orderId)->update(['snap_token' => $transaction->token]);
            DB::commit();

            return response()->json([
                'status'      => 'success',
                'snap_token'  => $transaction->token,
                'snap_url'    => $transaction->redirect_url,
                'final_price' => (int) $finalPrice,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Midtrans Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to create transaction'], 500);
        }
    }

    /**
     * ✨ 2. HANDLE NOTIFICATION (CALLBACK FROM MIDTRANS)
     */
    public function handleNotification(Request $request)
    {
        try {
            $notif       = new Notification();
            $order_id    = $notif->order_id;
            $transaction = $notif->transaction_status;

            $payment = DB::table('payments')->where('order_id', $order_id)->first();
            if (!$payment) {
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($transaction == 'settlement' || $transaction == 'capture') {

                $expiresAt = now()->addDays(140)->endOfDay();

                DB::transaction(function () use ($order_id, $notif, $payment, $expiresAt) {

                    // 1. Update payment table status
                    DB::table('payments')->where('order_id', $order_id)->update([
                        'status'       => 'success',
                        'payment_type' => $notif->payment_type,
                        'updated_at'   => now(),
                    ]);

                    // 2. Grant class access in enrollments table
                    DB::table('enrollments')->updateOrInsert(
                        ['user_id' => $payment->user_id, 'class_id' => $payment->class_id],
                        [
                            'status'     => 'active',
                            'expires_at' => $expiresAt,
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );

                    // ✨ 3. SYNC TO STUDENTS TABLE (IMPORTANT FOR FLUTTER)
                    // This line lets Flutter app know the user has an active class
                    DB::table('students')
                        ->where('user_id', $payment->user_id)
                        ->update(['class_id' => $payment->class_id]);

                    // 4. Reduce promo quota if applied
                    if ($payment->promo_code) {
                        DB::table('promotions')
                            ->where('code', $payment->promo_code)
                            ->where('class_id', $payment->class_id)
                            ->decrement('quota');
                    }
                });
            }

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            Log::error("Callback Error: " . $e->getMessage());
            return response()->json(['message' => 'Notification Error'], 500);
        }
    }
}
