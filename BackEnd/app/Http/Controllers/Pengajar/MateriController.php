<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use Illuminate\Http\Request;

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
        $materis = Material::where('class_id', $class_id)->get();

        return view('pengajar.materi.pilih', compact('class', 'materis'));
    }

    public function store(Request $request, $class_id)
    {
        $request->validate([
            'title'         => 'required',
            'material_name' => 'required|string|max:255', // Ubah nama_materi -> material_name
            'file_pdf'      => 'required|mimes:pdf|max:10240',
            'week'          => 'required|integer|min:1|max:20', // Ubah minggu -> week
        ]);

        $path = $request->file('file_pdf')->store('materi', 'public');

        Material::create([
            'class_id'      => $class_id,
            'title'         => $request->title,
            'material_name' => $request->material_name, // Map ke kolom baru
            'week'          => $request->week,          // Map ke kolom baru
            'file_path'     => $path,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan!');
    }
}
