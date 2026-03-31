<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoController extends Controller
{
    /**
     * Tampil Daftar Promo
     */
    public function index() {
        $promos = Promotion::latest()->get();
        return view('admin.promo.index', compact('promos'));
    }

    /**
     * Simpan Promo Baru (Mata Kuliah: Software Security & Cloud Storage)
     */
    public function store(Request $request) {
        $request->validate([
            'image_banner' => 'required|image|mimes:jpg,png,jpeg|max:2048', // Max 2MB
            'code' => 'required|string|unique:promotions,code',
            'discount_percent' => 'required|numeric|min:1|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // 1. Simpan Gambar Banner ke folder public/storage/promos
        $path = $request->file('image_banner')->store('promos', 'public');

        // 2. Simpan Data ke Database (Integritas Data)
        Promotion::create([
            'image_banner'     => $path,
            'code'             => strtoupper($request->code), // Paksa huruf besar agar rapi
            'discount_percent' => $request->discount_percent,
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'is_active'        => true
        ]);

        return back()->with('success', 'Promo Spekta Berhasil Diterbitkan!');
    }

    /**
     * Hapus Promo
     */
    public function destroy($id) {
        $promo = Promotion::findOrFail($id);

        // Hapus file fisik banner agar storage tidak penuh
        Storage::disk('public')->delete($promo->image_banner);

        $promo->delete();
        return back()->with('success', 'Promo telah dihapus.');
    }
}
