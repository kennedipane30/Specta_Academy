<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index() {
        return view('pengajar.materi.index');
    }

    public function store(Request $request) {
        // Logic upload file ke storage (Syarat Cloud Computing)
        return back()->with('success', 'Materi berhasil diunggah');
    }
}
