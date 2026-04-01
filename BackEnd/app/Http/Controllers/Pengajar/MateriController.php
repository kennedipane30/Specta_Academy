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
   public function store(Request $request, $material_id) {
    $request->validate([
        'file_pdf' => 'required|mimes:pdf|max:10240',
    ]);

    $material = Material::findOrFail($material_id);

    // LOGIKA PENCEGAHAN: Cek apakah file_path sudah terisi
    // Jika sudah ada, sampaikan bahwa materi harus di-edit/hapus dulu, jangan asal upload baru
    if ($material->file_path != null) {
        return back()->with('error', 'Materi ini sudah memiliki file. Silakan hapus file lama jika ingin mengganti.');
    }

    $path = $request->file('file_pdf')->store('materi', 'public');
    $material->update(['file_path' => $path]);

    return back()->with('success', 'Materi Berhasil Di-upload!');
}
}
