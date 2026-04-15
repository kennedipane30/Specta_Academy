<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class PromoController extends Controller
{
    public function index() {
        // classModel -> class
        $promos = Promotion::with('class')->latest()->get();
        $classes = ClassModel::all();
        return view('admin.promo.index', compact('promos', 'classes'));
    }

    public function store(Request $request) {
        $request->validate([
            'class_id' => 'required',
            'image_banner' => 'required|image|max:2048',
            'code' => 'required|unique:promotions,code',
            'discount_percent' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
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

    public function destroy($id) {
        $promo = Promotion::findOrFail($id);
        if ($promo->image_banner) { Storage::disk('public')->delete($promo->image_banner); }
        $promo->delete();
        return back()->with('success', 'Promo telah dihapus.');
    }

    public function apiIndex(): JsonResponse
    {
        $promos = Promotion::where('is_active', true)
                    ->with('class')
                    ->latest()
                    ->get()
                    ->map(function($item) {
                        $item->foto_url = "http://10.0.2.2:8000/view-galeri/" . basename($item->image_banner);
                        return $item;
                    });

        return response()->json(['status' => 'success', 'data' => $promos]);
    }
}
