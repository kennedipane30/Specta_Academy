<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index() {
        return view('admin.pembayaran.index'); // List bukti transfer dari siswa
    }

    public function verifikasi($id) {
        // Logic mengubah status bayar 'pending' menjadi 'success'
        return back()->with('success', 'Pembayaran Berhasil Diverifikasi');
    }

    public function promo() {
        return view('admin.pembayaran.promo'); // Input kode voucher diskon
    }
}
