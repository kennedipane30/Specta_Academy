<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Payment API Ready'
        ]);
    }

    public function getSnapToken(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'promo_code' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'User belum login'], 401);
        }

        $class = DB::table('classes')->where('class_id', $request->class_id)->first();
        if (!$class) {
            return response()->json(['message' => 'Class tidak ditemukan'], 404);
        }

        $basePrice = (int) ($class->price ?? 100000);
        $finalPrice = $basePrice;
        $discountAmount = 0;
        $appliedPromoId = null;

        if ($request->has('promo_code') && !empty($request->promo_code)) {
            $promo = DB::table('promotions')
                ->where('code', strtoupper($request->promo_code))
                ->where('class_id', $request->class_id)
                ->where('quota', '>', 0)
                ->where('is_active', 1)
                ->first();

            if ($promo) {
                if ($promo->discount_type == 'percent') {
                    $discountAmount = ($basePrice * $promo->discount_percent) / 100;
                } else {
                    $discountAmount = $promo->discount_percent; 
                }
                
                $finalPrice = $basePrice - $discountAmount;
                if ($finalPrice < 1) $finalPrice = 1;
                $appliedPromoId = $promo->promotion_id;
            }
        }

        $orderId = 'ORDER-' . time();

        try {
            DB::table('payments')->insert([
                'user_id'    => $user->usersID,
                'class_id'   => $class->class_id,
                'order_id'   => $orderId,
                'amount'     => $finalPrice,
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
                'item_details' => [
                    [
                        'id'       => $class->class_id,
                        'price'    => (int) $basePrice,
                        'quantity' => 1,
                        'name'     => substr('Kelas: ' . $class->program_name, 0, 45),
                    ]
                ],
            ];

            if ($discountAmount > 0) {
                $params['item_details'][] = [
                    'id'       => 'PROMO',
                    'price'    => (int) -$discountAmount,
                    'quantity' => 1,
                    'name'     => 'Diskon Promo',
                ];
            }

            $transaction = Snap::createTransaction($params);

            DB::table('payments')
                ->where('order_id', $orderId)
                ->update(['snap_token' => $transaction->token]);

            if ($appliedPromoId) {
                DB::table('promotions')->where('promotion_id', $appliedPromoId)->decrement('quota');
            }

            return response()->json([
                'snap_token' => $transaction->token,
                'snap_url'   => $transaction->redirect_url,
                'final_price' => $finalPrice
            ]);

        } catch (\Exception $e) {
            Log::error("Payment Error: " . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * 🔥 HANDLE MIDTRANS CALLBACK (NOTIFICATION)
     * Bagian ini yang diperbaiki agar payment_type tersimpan
     */
    public function handleNotification(Request $request)
{
    try {
        $notif = new Notification(); // Ini mengambil data dari Midtrans
        
        $order_id = $notif->order_id;
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type; // <--- Mengambil tipe (gopay, bank_transfer, dll)

        $payment = DB::table('payments')->where('order_id', $order_id)->first();
        if (!$payment) return response()->json(['message' => 'Not found'], 404);

        if ($transaction == 'settlement' || $transaction == 'capture') {
            // ✅ PERBAIKAN: Masukkan payment_type ke dalam update
            DB::table('payments')->where('order_id', $order_id)->update([
                'status' => 'success',
                'payment_type' => $type, // Sekarang tipe pembayaran akan tersimpan
                'updated_at' => now()
            ]);

            // Berikan akses kelas
            DB::table('enrollments')->updateOrInsert(
                ['user_id' => $payment->user_id, 'class_id' => $payment->class_id],
                ['status' => 'active', 'updated_at' => now()]
            );
        } 
        else if (in_array($transaction, ['deny', 'expire', 'cancel'])) {
            DB::table('payments')->where('order_id', $order_id)->update([
                'status' => 'failed',
                'payment_type' => $type, // Tetap simpan tipe meskipun gagal
                'updated_at' => now()
            ]);
        }

        return response()->json(['status' => 'OK']);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }
}
}