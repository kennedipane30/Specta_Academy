<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\ClassModel;
use Carbon\Carbon;

class PromoController extends Controller
{
    /**
     * Tampilan untuk Web Admin
     */
    public function index()
    {
        $classes = ClassModel::orderBy('program_name')->get();
        // Eager loading relasi class agar performa cepat
        $promos = Promotion::with('class')->orderBy('created_at', 'desc')->get();
        
        return view('admin.promo.index', compact('classes', 'promos'));
    }

    /**
     * Simpan Promo dari Web Admin
     */
    public function store(Request $request)
{
    // 1. Ubah input kode menjadi HURUF BESAR dulu sebelum dicek validasi
    $request->merge([
        'code' => strtoupper($request->code)
    ]);

    // 2. Sekarang validasi 'unique' akan mengecek huruf besar yang sudah disamakan
    $request->validate([
        'code'           => 'required|string|unique:promotions,code', 
        'class_id'       => 'required|exists:classes,class_id',
        'discount_type'  => 'required|in:percent,fixed',
        'discount_value' => 'required|numeric|min:0',
        'quota'          => 'required|integer|min:1',
        'start_date'     => 'required|date',
        'end_date'       => 'required|date|after_or_equal:start_date',
    ], [
        'code.unique' => 'Gagal! Kode promo "' . $request->code . '" sudah ada di database. Silakan gunakan kode lain.',
    ]);

    // 3. Eksekusi simpan
    Promotion::create([
        'code'             => $request->code, // Sudah otomatis besar dari proses merge di atas
        'class_id'         => $request->class_id,
        'discount_type'    => $request->discount_type,
        'discount_percent' => $request->discount_value, 
        'quota'            => $request->quota,
        'start_date'       => $request->start_date,
        'end_date'         => $request->end_date,
        'is_active'        => 1
    ]);

    return redirect()->back()->with('success', 'Kode promo baru berhasil diterbitkan!');
}
    /**
     * Hapus Promo
     */
    public function destroy($id)
    {
        $promo = Promotion::findOrFail($id);
        $promo->delete();
        
        return redirect()->back()->with('success', 'Kode promo berhasil dihapus dari sistem.');
    }

    /**
     * 🔥 FUNGSI UNTUK MOBILE (API CHECK PROMO)
     */
    public function checkPromo(Request $request)
    {
        // 1. Validasi Input API
        $request->validate([
            'code'     => 'required|string',
            'class_id' => 'required|integer'
        ]);

        $today = Carbon::today()->toDateString();

        // 2. Cari promo yang aktif dan valid
        $promo = Promotion::where('code', strtoupper($request->code))
            ->where('class_id', $request->class_id)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('quota', '>', 0)
            ->where('is_active', 1)
            ->first();

        if (!$promo) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Maaf, kode promo tidak ditemukan, sudah kedaluwarsa, atau kuota habis.'
            ], 404);
        }

        // 3. Cari harga asli kelas
        $kelas = ClassModel::find($request->class_id);
        if (!$kelas) {
            return response()->json(['status' => 'error', 'message' => 'Data kelas tidak ditemukan'], 404);
        }

        $basePrice = (int) $kelas->price;
        $discountAmount = 0;

        // 4. Logika Hitung Diskon
        if ($promo->discount_type == 'percent') {
            // Diskon persentase (Contoh: 10% dari 150rb = 15rb)
            $discountAmount = ($basePrice * $promo->discount_percent) / 100;
        } else {
            // Diskon nominal tetap (Contoh: Potongan langsung 50rb)
            $discountAmount = $promo->discount_percent; 
        }

        $finalPrice = $basePrice - $discountAmount;
        
        // Pastikan harga tidak minus
        if ($finalPrice < 0) $finalPrice = 0;

        return response()->json([
            'status'          => 'success',
            'promo_id'        => $promo->promotion_id,
            'code'            => $promo->code,
            'discount_amount' => (int) $discountAmount,
            'base_price'      => $basePrice,
            'final_price'     => (int) $finalPrice
        ]);
    }
}