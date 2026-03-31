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
    $promos = Promotion::with('classModel')->latest()->get();
    $classes = \App\Models\ClassModel::all(); // Ambil 4 program Spekta
    return view('admin.promo.index', compact('promos', 'classes'));
}

public function store(Request $request) {
    $request->validate([
        'class_id' => 'required', // Validasi pilihan kelas
        'image_banner' => 'required|image|max:2048',
        'code' => 'required|unique:promotions,code',
        'discount_percent' => 'required|numeric',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
    ]);

    $path = $request->file('image_banner')->store('promos', 'public');

    Promotion::create([
        'class_id'         => $request->class_id,
        'image_banner'     => $path,
        'code'             => strtoupper($request->code),
        'discount_percent' => $request->discount_percent,
        'start_date'       => $request->start_date,
        'end_date'         => $request->end_date,
    ]);

    return back()->with('success', 'Promo Berhasil Diterbitkan!');
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
