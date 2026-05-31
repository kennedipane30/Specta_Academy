<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    /**
     * --- BAGIAN WEB ADMIN ---
     */

    public function index()
    {
        $classes = ClassModel::paginate(10);
        return view('admin.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('admin.classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'program_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('image')->store('classes', 'public');

        ClassModel::create([
            'program_name' => $request->program_name,
            'price'        => (int) $request->price, // Pastikan tersimpan sebagai integer
            'description'  => $request->description,
            'image'        => $path,
            'image_url'    => asset('storage/' . $path),
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Program baru berhasil dipublikasikan.');
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
            'price'        => 'required|numeric|min:0',
            'description'  => 'required',
            'image'        => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['program_name', 'price', 'description']);
        $data['price'] = (int) $request->price; // Paksa jadi integer

        if ($request->hasFile('image')) {
            if ($class->image) {
                Storage::disk('public')->delete($class->image);
            }
            
            $path = $request->file('image')->store('classes', 'public');
            $data['image'] = $path;
            $data['image_url'] = asset('storage/' . $path);
        }

        $class->update($data);

        return redirect()->route('admin.classes.index')->with('success', 'Konfigurasi program berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        if ($class->image) {
            Storage::disk('public')->delete($class->image);
        }
        $class->delete();

        return back()->with('success', 'Program telah dihapus dari katalog.');
    }


    /**
     * --- BAGIAN API UNTUK FLUTTER ---
     * Tambahkan fungsi ini agar Flutter mendapatkan data yang benar
     */

    // API Untuk Daftar Kelas di Home Flutter
    public function apiIndex()
    {
        $classes = ClassModel::all();
        return response()->json([
            'status' => 'success',
            'data' => $classes
        ]);
    }

    // API Untuk Detail Kelas di Flutter (Mencegah Rp 0)
    public function apiShow($id)
    {
        $class = ClassModel::find($id);

        if (!$class) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'class_id'     => $class->class_id,
                'program_name' => $class->program_name,
                'price'        => (int) $class->price, // Kirim sebagai integer agar tidak 0
                'description'  => $class->description,
                'image_url'    => $class->image_url,
            ]
        ]);
    }
}