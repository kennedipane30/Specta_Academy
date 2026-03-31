<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GaleriController extends Controller
{
    // 1. Tampil Semua (Web Admin)
    public function index() {
        $galeri = Gallery::latest()->get();
        return view('admin.galeri.index', compact('galeri'));
    }

    // 2. Simpan (Web Admin)
    public function store(Request $request) {
        $request->validate([
            'judul' => 'required',
            'foto' => 'required|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $path = $request->file('foto')->store('galeri', 'public');
        Gallery::create([
            'judul' => $request->judul,
            'foto' => $path,
            'deskripsi' => $request->deskripsi
        ]);

        return back()->with('success', 'Foto Berhasil Ditambahkan!');
    }

    // 3. Edit Halaman (Web Admin)
    public function edit($id) {
        $item = Gallery::findOrFail($id);
        return view('admin.galeri.edit', compact('item'));
    }

    // 4. Update (Web Admin)
    public function update(Request $request, $id) {
        $item = Gallery::findOrFail($id);
        $request->validate(['judul' => 'required']);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi
        ];

        if ($request->hasFile('foto')) {
            Storage::disk('public')->delete($item->foto); // Hapus foto lama
            $data['foto'] = $request->file('foto')->store('galeri', 'public');
        }

        $item->update($data);
        return redirect()->route('admin.galeri.index')->with('success', 'Galeri Diperbarui!');
    }

    // 5. Hapus (Web Admin)
    public function destroy($id) {
        $item = Gallery::findOrFail($id);
        Storage::disk('public')->delete($item->foto);
        $item->delete();
        return back()->with('success', 'Foto Berhasil Dihapus!');
    }

    // 6. API UNTUK MOBILE (Siswa - Muncul 14 Hari Saja)
    public function apiIndex() {
        $batasWaktu = now()->subDays(14);

        $galeri = \App\Models\Gallery::where('created_at', '>=', $batasWaktu)
                        ->latest()
                        ->get()
                        ->map(function($item) {
                            // KITA GUNAKAN JALUR RESMI YANG BARU DIBUAT
                            // Ambil hanya nama filenya saja dari kolom foto
                            $filename = basename($item->foto);
                            $item->foto_url = "http://10.0.2.2:8000/view-galeri/" . $filename;
                            return $item;
                        });

        return response()->json([
            'status' => 'success',
            'data'   => $galeri
        ]);
    }
}
