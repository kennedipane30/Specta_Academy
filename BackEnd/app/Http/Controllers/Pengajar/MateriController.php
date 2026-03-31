<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada

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

    /**
     * FUNGSI UPLOAD (Sesuai Alur: Hanya Simpan)
     */
    public function store(Request $request, $material_id)
    {
        // 1. Validasi File (Max 10MB PDF)
        $request->validate([
            'file_pdf' => 'required|mimes:pdf|max:10240',
        ]);

        // 2. Cari data materi berdasarkan ID kustom (materialsID)
        $material = Material::findOrFail($material_id);

        // 3. Hapus file lama jika pengajar ingin menimpa (Integritas Storage)
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        // 4. Simpan file baru ke public/storage/materi
        $path = $request->file('file_pdf')->store('materi', 'public');

        // 5. Update Path di Database
        $material->update([
            'file_path' => $path
        ]);

        return back()->with('success', 'Berhasil! Materi ' . $material->title . ' telah di-upload.');
    }
}
