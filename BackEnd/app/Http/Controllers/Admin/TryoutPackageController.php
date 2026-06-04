<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Tryout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TryoutPackageController extends Controller
{
    /**
     * URL Go Service untuk Tryout
     */
    private function goUrl(): string
    {
        return env('GO_TRYOUT_URL', 'http://127.0.0.1:9003');
    }

    /**
     * 1. DAFTAR PAKET
     */
    public function index()
    {
        // Mengambil paket dengan hitungan soal dan nama kelasnya
        $tryouts = Tryout::with('classModel')
                    ->withCount('questions')
                    ->latest()
                    ->paginate(10);

        return view('admin.tryout.index', compact('tryouts'));
    }
    
    /**
     * 2. FORM TAMBAH PAKET
     */
    public function create()
    {
        // ✨ DINAMIS: Mengambil semua kelas (7, 8, 9, dst) dari database
        // Apapun kelas yang Anda buat di menu Program akan muncul di sini otomatis.
        $classes = ClassModel::orderBy('program_name')->get();
        return view('admin.tryout.create', compact('classes'));
    }
    
    /**
     * 3. SIMPAN PAKET & SYNC KE GO SERVICE
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'class_id'  => 'required', 
            'duration'  => 'required|integer',
            'is_active' => 'required|boolean'
        ]);

        try {
            // A. Simpan di Database Laravel (Admin)
            $tryout = Tryout::create([
                'title'     => $request->title,
                'class_id'  => $request->class_id,
                'duration'  => $request->duration,
                'is_active' => $request->is_active,
            ]);

            // B. ✨ SYNC KE GO SERVICE (Agar Siswa bisa melihat di HP)
            // Ini memastikan kelas 7, 8, dll mendapatkan data secara real-time
            $syncResponse = Http::post($this->goUrl() . '/api/tryouts/sync', [
                'id'        => (int) $tryout->id,
                'title'     => $tryout->title,
                'class_id'  => (int) $tryout->class_id,
                'duration'  => (int) $tryout->duration,
                'is_active' => (bool) $tryout->is_active,
            ]);

            if (!$syncResponse->successful()) {
                Log::error("Gagal Sinkronisasi TO ke Go Service: " . $syncResponse->body());
            }

            return redirect()->route('admin.tryout_package.index')
                             ->with('success', 'Paket Tryout berhasil diterbitkan dan disinkronkan ke aplikasi siswa.');

        } catch (\Exception $e) {
            Log::error("Error Store Tryout: " . $e->getMessage());
            return back()->with('error', 'Gagal menerbitkan paket: ' . $e->getMessage());
        }
    }
    
    /**
     * 4. FORM EDIT
     */
    public function edit($id)
    {
        $tryout = Tryout::findOrFail($id);
        $classes = ClassModel::orderBy('program_name')->get();
        return view('admin.tryout.edit', compact('tryout', 'classes'));
    }
    
    /**
     * 5. UPDATE PAKET & SYNC ULANG
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'class_id'  => 'required',
            'duration'  => 'required|integer',
            'is_active' => 'required|boolean'
        ]);

        try {
            $tryout = Tryout::findOrFail($id);
            
            // A. Update Lokal
            $tryout->update($request->all());

            // B. ✨ UPDATE SYNC KE GO SERVICE
            Http::post($this->goUrl() . '/api/tryouts/sync', [
                'id'        => (int) $tryout->id,
                'title'     => $tryout->title,
                'class_id'  => (int) $tryout->class_id,
                'duration'  => (int) $tryout->duration,
                'is_active' => (bool) $tryout->is_active,
            ]);

            return redirect()->route('admin.tryout_package.index')
                             ->with('success', 'Paket Tryout berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update paket: ' . $e->getMessage());
        }
    }
    
    /**
     * 6. HAPUS PAKET
     */
    public function destroy($id)
    {
        try {
            $tryout = Tryout::findOrFail($id);

            // A. Hapus di Go Service dahulu
            Http::delete($this->goUrl() . '/api/tryouts/' . $id);

            // B. Hapus Lokal
            $tryout->delete();

            return redirect()->route('admin.tryout_package.index')
                             ->with('success', 'Paket Tryout berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}