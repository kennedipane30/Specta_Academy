<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        return view('pengajar.materi.index', compact('classes'));
    }

    public function pilihMateri($class_id)
    {
        $class = ClassModel::findOrFail($class_id);
        // Mengambil semua materi untuk kelas ini agar bisa ditampilkan di list bawah
        $materis = Material::where('class_id', $class_id)->get();

        return view('pengajar.materi.pilih', compact('class', 'materis'));
    }

    public function store(Request $request, $class_id)
        {
            $request->validate([
                'title'       => 'required', // Ini kategori (Bahasa Inggris, dll)
                'nama_materi' => 'required|string|max:255', // Ini judul spesifiknya
                'file_pdf'    => 'required|mimes:pdf|max:10240',
                'minggu'      => 'required|integer|min:1|max:20',
            ]);

            $path = $request->file('file_pdf')->store('materi', 'public');

            \App\Models\Material::create([
                'class_id'    => $class_id,
                'title'       => $request->title,
                'nama_materi' => $request->nama_materi,
                'minggu'      => $request->minggu,
                'file_path'   => $path,
            ]);

            return back()->with('success', 'Modul berhasil ditambahkan!');
        }
}
