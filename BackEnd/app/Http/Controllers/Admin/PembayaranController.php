<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index() { return view('admin.pembayaran.index'); }
    public function verifikasi($id) { return back()->with('success', 'Pembayaran Berhasil Diverifikasi'); }
    public function promo() { return view('admin.pembayaran.promo'); }
}
