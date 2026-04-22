<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\ClassModel; // Gunakan ClassModel sesuai file yang Anda miliki

class PromoController extends Controller
{
    /**
     * Tampilan Web Admin
     */
    public function index()
    {
        // Pastikan menggunakan ClassModel, bukan Classes
        $classes = ClassModel::all();
        $promos = Promotion::with('class')->orderBy('created_at', 'desc')->get();
        
        return view('admin.promo.index', compact('classes', 'promos'));
    }

    /**
     * Simpan Promo dari Web Admin
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:promotions,code',
            'class_id' => 'required',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric',
            'quota' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Promotion::create([
            'code'           => strtoupper($request->code),
            'class_id'       => $request->class_id,
            'discount_type'  => $request->discount_type,
            // Simpan ke kolom discount_percent (sesuai struktur DB Anda di pgAdmin)
            'discount_percent' => $request->discount_value, 
            'quota'          => $request->quota,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'is_active'      => 1
        ]);

        return redirect()->back()->with('success', 'Kode promo berhasil diterbitkan!');
    }

    /**
     * Hapus Promo
     */
    public function destroy($id)
    {
        $promo = Promotion::findOrFail($id);
        $promo->delete();

        return redirect()->back()->with('success', 'Kode promo berhasil dihentikan!');
    }

    /**
     * 🔥 FUNGSI UNTUK API FLUTTER (CEK PROMO)
     * Inilah yang dicari oleh aplikasi mobile Anda
     */
    public function checkPromo(Request $request)
    {
        // 1. Validasi input dari Flutter
        $request->validate([
            'code' => 'required',
            'class_id' => 'required'
        ]);

        // 2. Cari promo yang aktif, sesuai kelas, tanggal valid, dan kuota tersedia
        $promo = Promotion::where('code', strtoupper($request->code))
            ->where('class_id', $request->class_id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('quota', '>', 0)
            ->where('is_active', 1)
            ->first();

        if (!$promo) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kode promo tidak valid, kadaluarsa, atau kuota habis.'
            ], 404);
        }

        // 3. Ambil data kelas untuk hitung harga asli
        $kelas = ClassModel::find($request->class_id);
        if (!$kelas) {
            return response()->json(['message' => 'Data kelas tidak ditemukan'], 404);
        }

        $basePrice = (int) $kelas->price;
        $discountAmount = 0;

        // 4. Logika Hitung Diskon
        if ($promo->discount_type == 'percent') {
            // Diskon persen (misal 10%)
            $discountAmount = ($basePrice * $promo->discount_percent) / 100;
        } else {
            // Diskon nominal (misal Rp 50.000)
            $discountAmount = $promo->discount_percent; 
        }

        $finalPrice = $basePrice - $discountAmount;
        
        // Pastikan harga tidak negatif
        if ($finalPrice < 0) $finalPrice = 0;

        return response()->json([
            'status' => 'success',
            'discount_amount' => (int) $discountAmount,
            'final_price' => (int) $finalPrice,
            'promo_id' => $promo->promotion_id
        ]);
    }
}