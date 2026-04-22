<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClassManagementController extends Controller
{
    public function index()
    {
        $classes = ClassModel::all();
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        // 1. Validasi Ketat
        $request->validate([
            'program_name' => 'required|string|max:255',
            'price'        => 'required|numeric',
            'description'  => 'required|string',
            'banner_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        // 2. Inisialisasi Model Baru
        $class = new ClassModel();
        $class->program_name = $request->program_name;
        $class->price = $request->price;
        $class->description = $request->description;

        // 3. Proses Gambar
        if ($request->hasFile('banner_image')) {
            // Simpan ke storage/app/public/class_banners
            $path = $request->file('banner_image')->store('class_banners', 'public');
            
            // Hasilkan link: http://127.0.0.1:8000/storage/class_banners/xxx.jpg
            $fullUrl = asset('storage/' . $path);
            
            $class->image_url = $fullUrl;
            $class->image = $fullUrl; // Mengisi kolom wajib agar tidak error NULL
        }

        // 4. Eksekusi Simpan
        $class->save();

        return redirect()->route('admin.classes.index')->with('success', 'Program Baru Berhasil Dipublikasikan!');
    }

    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        return view('admin.classes.edit', compact('class'));
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $request->validate([
            'program_name' => 'required|string|max:255',
            'price'        => 'required|numeric',
            'description'  => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $class->program_name = $request->program_name;
        $class->price = $request->price;
        $class->description = $request->description;

        if ($request->hasFile('banner_image')) {
            // Hapus file lama jika ada agar tidak penuh
            if ($class->image_url) {
                $oldFile = basename($class->image_url);
                Storage::disk('public')->delete('class_banners/' . $oldFile);
            }

            $path = $request->file('banner_image')->store('class_banners', 'public');
            $fullUrl = asset('storage/' . $path);
            
            $class->image_url = $fullUrl;
            $class->image = $fullUrl; 
        }

        $class->save();

        return redirect()->route('admin.classes.index')->with('success', 'Data Program Berhasil Diperbarui!');
    }

    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);

        if ($class->image_url) {
            $file = basename($class->image_url);
            Storage::disk('public')->delete('class_banners/' . $file);
        }

        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Program Berhasil Dihapus!');
    }
}