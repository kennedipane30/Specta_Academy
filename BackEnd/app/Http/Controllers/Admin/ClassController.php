<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassController extends Controller
{
    /**
     * Menampilkan daftar semua kelas (4 kelas Spekta).
     */
    public function index() 
    {
        $classes = Kelas::all(); 
        return view('admin.classes.index', compact('classes'));
    }

    /**
     * Membuka halaman edit untuk kelas tertentu.
     */
    public function edit($id) 
    {
        $class = Kelas::findOrFail($id);
        return view('admin.classes.edit', compact('class'));
    }

    /**
     * Memproses pembaruan data Harga, Deskripsi, dan Banner Gambar.
     */
    public function update(Request $request, $id) 
    {
        // 1. Validasi Input
        $request->validate([
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Max 2MB
        ]);

        $class = Kelas::findOrFail($id);
        
        // Simpan data teks ke array
        $updateData = [
            'price' => $request->price,
            'description' => $request->description,
        ];

        // 2. Logika Upload Gambar (Jika Admin mengupload file baru)
        if ($request->hasFile('banner_image')) {
            
            // Hapus gambar lama dari folder storage jika ada (agar hemat ruang)
            if ($class->image_url) {
                // Mengambil nama file dari URL lama
                $oldPath = str_replace(asset('storage/'), 'public/', $class->image_url);
                Storage::delete($oldPath);
            }

            // Simpan gambar baru ke folder: storage/app/public/class_banners
            $path = $request->file('banner_image')->store('public/class_banners');
            
            // Dapatkan URL publik lengkap (Contoh: http://domain.com/storage/class_banners/namafile.jpg)
            // asset() memastikan Flutter bisa langsung menampilkan gambar tersebut
            $updateData['image_url'] = asset(Storage::url($path));
        }

        // 3. Eksekusi Update ke Database
        $class->update($updateData);

        // Kembali ke daftar kelas dengan pesan sukses
        return redirect()->route('admin.classes.index')
                         ->with('success', 'Konten Kelas ' . $class->name . ' Berhasil Diperbarui!');
    }
}