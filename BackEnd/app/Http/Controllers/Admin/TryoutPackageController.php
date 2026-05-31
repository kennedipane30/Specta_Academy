<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Tryout;
use Illuminate\Http\Request;

class TryoutPackageController extends Controller
{
    public function index()
    {
        $tryouts = Tryout::withCount('questions')->latest()->paginate(10);
        return view('admin.tryout.index', compact('tryouts'));
    }
    
    public function create()
    {
        $classes = ClassModel::all();
        return view('admin.tryout.create', compact('classes'));
    }
    
    public function store(Request $request)
    {
        // Akan diisi nanti untuk upload Excel
        return back()->with('info', 'Fitur upload Excel sedang dalam pengembangan');
    }
    
    public function edit($id)
    {
        $tryout = Tryout::findOrFail($id);
        $classes = ClassModel::all();
        return view('admin.tryout.edit', compact('tryout', 'classes'));
    }
    
    public function update(Request $request, $id)
    {
        $tryout = Tryout::findOrFail($id);
        $tryout->update($request->all());
        return redirect()->route('admin.tryout_package.index')->with('success', 'Tryout berhasil diupdate');
    }
    
    public function destroy($id)
    {
        $tryout = Tryout::findOrFail($id);
        $tryout->delete();
        return redirect()->route('admin.tryout_package.index')->with('success', 'Tryout berhasil dihapus');
    }
    
    public function downloadTemplate()
    {
        // Template Excel download
        $headers = ['No', 'Pertanyaan', 'Gambar Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Kunci Jawaban', 'Pembahasan', 'Poin'];
        // ... akan dilengkapi
        return response()->json(['message' => 'Template download akan segera tersedia']);
    }
}