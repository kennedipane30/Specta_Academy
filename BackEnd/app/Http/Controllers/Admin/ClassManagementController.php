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
        // Menggunakan paginate agar sinkron dengan template Blade (.cp-pagination)
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
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload ke folder public/storage/classes
        $path = $request->file('image')->store('classes', 'public');

        ClassModel::create([
            'program_name' => $request->program_name,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $path,
            'image_url' => asset('storage/' . $path), // Untuk kebutuhan Flutter
        ]);

        return redirect()->route('admin.classes.index')->with('success', 'Program kelas berhasil ditambahkan ke katalog.');
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
            'price' => 'required|numeric',
            'description' => 'required',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['program_name', 'price', 'description']);

        if ($request->hasFile('image')) {
            // Hapus gambar lama    
            if ($class->image) {
                Storage::disk('public')->delete($class->image);
            }
            
            $path = $request->file('image')->store('classes', 'public');
            $data['image'] = $path;
            $data['image_url'] = asset('storage/' . $path);
        }

        $class->update($data);

        return redirect()->route('admin.classes.index')->with('success', 'Informasi program berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        if ($class->image) {
            Storage::disk('public')->delete($class->image);
        }
        $class->delete();

        return back()->with('success', 'Program berhasil dihapus.');
    }
}