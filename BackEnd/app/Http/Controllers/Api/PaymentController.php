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

    /**
     * ✨ 1. GET SNAP TOKEN
     * Logika: Mencegah user mendaftar lebih dari 1 kelas aktif.
     */
    public function getSnapToken(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'promo_code' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Sesi login berakhir'], 401);
        }

        // 1. CEK KEBIJAKAN 1 USER = 1 KELAS AKTIF
        // Mengecek apakah user sudah punya pendaftaran aktif di KELAS MANAPUN
        $anyActiveEnrollment = DB::table('enrollments')
            ->where('user_id', $user->usersID)
            ->where('status', 'active')
            ->exists();

        if ($anyActiveEnrollment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Maaf, Anda sudah memiliki program kelas yang aktif. Setiap siswa hanya diperbolehkan mengikuti 1 program dalam satu waktu.'
            ], 400);
        }

        // 2. Ambil data kelas yang ingin dibeli
        $class = DB::table('classes')->where('class_id', $request->class_id)->first();
        if (!$class) return response()->json(['message' => 'Program tidak ditemukan'], 404);

        $basePrice = (int) $class->price;
        $finalPrice = $basePrice;
        $appliedPromoCode = null;

        // 3. Logika Promo (Double check di Backend)
        if ($request->has('promo_code') && !empty($request->promo_code)) {
            $promoCode = strtoupper($request->promo_code);
            $promo = DB::table('promotions')
                ->where('code', $promoCode)
                ->where('class_id', $request->class_id)
                ->where('quota', '>', 0)
                ->where('is_active', 1)
                ->first();

            // Cek apakah user pernah pakai promo ini sebelumnya
            $alreadyUsedPromo = DB::table('payments')
                ->where('user_id', $user->usersID)
                ->where('promo_code', $promoCode)
                ->whereIn('status', ['success', 'pending'])
                ->exists();

            if ($promo && !$alreadyUsedPromo) {
                $discount = ($basePrice * $promo->discount_percent) / 100;
                $finalPrice = $basePrice - $discount;
                
                // Midtrans minimal transaksi adalah Rp 1.000
                if ($finalPrice < 1000) $finalPrice = 1000;
                
                $appliedPromoCode = $promoCode;
            }
        }

        $orderId = 'ORDER-' . time() . '-' . $user->usersID;

        try {
            DB::beginTransaction();

            // Simpan record pembayaran awal
            DB::table('payments')->insert([
                'user_id'    => $user->usersID,
                'class_id'   => $class->class_id,
                'order_id'   => $orderId,
                'amount'     => $finalPrice,
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
                'item_details' => [
                    [
                        'id'       => $class->class_id,
                        'price'    => (int) $finalPrice,
                        'quantity' => 1,
                        'name'     => substr("Spekta: " . $class->program_name, 0, 45),
                    ]
                ],
            ];

            $transaction = Snap::createTransaction($params);

            // Update Snap Token ke database
            DB::table('payments')
                ->where('order_id', $order_id)
                ->update(['snap_token' => $transaction->token]);

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'snap_token' => $transaction->token,
                'snap_url'   => $transaction->redirect_url,
                'final_price' => $finalPrice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Midtrans Snap Error: " . $e->getMessage());
            return response()->json(['message' => 'Gagal membuat transaksi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ✨ 2. HANDLE MIDTRANS CALLBACK
     */
    public function handleNotification(Request $request)
    {
        try {
            $notif = new Notification();
            $order_id = $notif->order_id;
            $transaction = $notif->transaction_status;

            $payment = DB::table('payments')->where('order_id', $order_id)->first();
            if (!$payment) return response()->json(['message' => 'Order not found'], 404);

            if ($transaction == 'settlement' || $transaction == 'capture') {
                DB::transaction(function () use ($order_id, $notif, $payment) {
                    
                    // 1. Update Payment
                    DB::table('payments')->where('order_id', $order_id)->update([
                        'status' => 'success',
                        'payment_type' => $notif->payment_type,
                        'updated_at' => now()
                    ]);

                    // 2. Berikan akses kelas di tabel Enrollments
                    DB::table('enrollments')->updateOrInsert(
                        ['user_id' => $payment->user_id, 'class_id' => $payment->class_id],
                        [
                            'status' => 'active',
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );

                    // 3. Potong kuota promo jika transaksi sukses
                    if ($payment->promo_code) {
                        DB::table('promotions')
                            ->where('code', $payment->promo_code)
                            ->where('class_id', $payment->class_id)
                            ->decrement('quota');
                    }
                });
                
                Log::info("Payment Success: " . $order_id);
            } 
            
            return response()->json(['status' => 'OK']);
            
        } catch (\Exception $e) {
            Log::error("Midtrans Callback Error: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}