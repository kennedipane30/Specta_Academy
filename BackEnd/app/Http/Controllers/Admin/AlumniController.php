<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    public function index() {
        $alumni = Alumni::latest()->get();
        return view('admin.alumni.index', compact('alumni'));
    }

    public function store(Request $request) {
        $request->validate([
            'nama' => 'required',
            'berhasil_menjadi' => 'required',
            'foto' => 'required|image|mimes:jpg,png,jpeg|max:2048'
        ]);

        $path = $request->file('foto')->store('alumni', 'public');

        Alumni::create([
            'nama' => $request->nama,
            'berhasil_menjadi' => $request->berhasil_menjadi,
            'foto' => $path
        ]);

        return back()->with('success', 'Data Alumni Berhasil Ditambahkan!');
    }

    // Menampilkan halaman edit
        public function edit($id)
        {
            $alumni = Alumni::findOrFail($id);
            return view('admin.alumni.edit', compact('alumni'));
        }

        // Memproses perubahan data
        public function update(Request $request, $id) {
            $request->validate([
                'nama' => 'required',
                'berhasil_menjadi' => 'required',
                'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
            ]);

            $alumni = Alumni::findOrFail($id);

            // Cek jika ada upload foto baru
            if ($request->hasFile('foto')) {
                // Hapus foto lama
                Storage::disk('public')->delete($alumni->foto);
                // Simpan foto baru
                $path = $request->file('foto')->store('alumni', 'public');
                $alumni->foto = $path;
            }

            $alumni->update([
                'nama' => $request->nama,
                'berhasil_menjadi' => $request->berhasil_menjadi,
            ]);

            return redirect()->route('admin.alumni.index')->with('success', 'Data Alumni Berhasil Diperbarui!');
        }

    public function destroy($id) {
        $data = Alumni::findOrFail($id);
        Storage::disk('public')->delete($data->foto);
        $data->delete();
        return back()->with('success', 'Data Alumni Berhasil Dihapus!');
    }
}
