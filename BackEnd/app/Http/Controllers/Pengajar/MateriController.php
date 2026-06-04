<?php

namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use App\Models\TeacherAssignment;
use App\Models\Subject; // Pastikan Model Subject diimport
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    /**
     * Tampilkan daftar kelas dan mata pelajaran yang ditugaskan (SINKRON MATRIX)
     */
    public function index()
    {
        // PERBAIKAN: Eager load 'subject' agar nampak di tabel
        $assignments = TeacherAssignment::with(['classModel', 'subject'])
            ->where('user_id', Auth::user()->usersID)
            ->get();

        return view('pengajar.materi.index', compact('assignments'));
    }

    /**
     * Tampilkan materi mingguan untuk mapel tertentu berdasarkan ID
     */
    public function pilihMateri($class_id, $subject_id)
    {
        // Cek akses guru terhadap ID Mapel dan Kelas di Matrix
        $access = TeacherAssignment::where('user_id', Auth::user()->usersID)
            ->where('class_id', $class_id)
            ->where('subject_id', $subject_id)
            ->first();

        if (!$access) abort(403, 'Akses Ditolak! Anda tidak ditugaskan untuk mata pelajaran ini.');

        $class = ClassModel::findOrFail($class_id);
        $subject = Subject::findOrFail($subject_id);

        // Ambil materi berdasarkan Nama Mapel agar sinkron dengan data yang sudah ada
        $materis = Material::where('class_id', $class_id)
            ->where('material_name', $subject->name)
            ->orderBy('week', 'asc')->get();

        $subject_name = $subject->name;

        return view('pengajar.materi.pilih', compact('class', 'materis', 'subject_name', 'subject_id'));
    }

    /**
     * Simpan materi baru
     */
    public function store(Request $request, $class_id)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'material_name' => 'required|string', // Ini adalah Nama Mapel (TIU, TWK, dll)
            'file_pdf'      => 'nullable|mimes:pdf|max:10240',
            'week'          => 'required|integer|min:1|max:20',
        ]);

        $existingMaterial = Material::where('class_id', $class_id)
            ->where('material_name', $request->material_name)
            ->where('week', $request->week)
            ->first();

        $dataLocal = [
            'class_id'      => (int)$class_id,
            'user_id'       => Auth::user()->usersID,
            'title'         => $request->title,
            'material_name' => $request->material_name,
            'week'          => (int)$request->week,
        ];

        if ($request->hasFile('file_pdf')) {
            if ($existingMaterial && $existingMaterial->file_path) {
                Storage::disk('public')->delete($existingMaterial->file_path);
            }
            $path = $request->file('file_pdf')->store('materi', 'public');
            $dataLocal['file_path'] = $path;
        }

        try {
            if ($existingMaterial) {
                $existingMaterial->update($dataLocal);
                $this->syncToGo($existingMaterial, 'PUT');
                return back()->with('success', 'Materi Minggu ' . $request->week . ' berhasil diperbarui!');
            } else {
                if (!$request->hasFile('file_pdf')) {
                    return back()->with('error', 'File PDF wajib diunggah untuk materi baru.');
                }

                $newMaterial = Material::create($dataLocal);
                $response = $this->syncToGo($newMaterial, 'POST');

                if (!$response->successful()) {
                    $this->syncToGo($newMaterial, 'PUT');
                }

                return back()->with('success', 'Materi baru berhasil ditambahkan!');
            }
        } catch (\Exception $e) {
            Log::error("Sync Error: " . $e->getMessage());
            return back()->with('success', 'Tersimpan lokal (Server Go Offline).');
        }
    }

    /**
     * Helper sinkronisasi ke Microservice Go
     */
    private function syncToGo($material, $method)
    {
        $url = env('GO_MATERI_URL') . '/api/materials';
        
        $payload = [
            'material_id'   => (int) $material->material_id,
            'class_id'      => (int) $material->class_id,
            'subject_name'  => $material->material_name, 
            'title'         => $material->title,
            'week'          => (int) $material->week,
            'file_path'     => $material->file_path,
        ];

        if ($method === 'PUT') {
            return Http::put($url . '/' . $material->material_id, $payload);
        }

        return Http::post($url, $payload);
    }

    /**
     * Hapus Materi
     */
    public function destroy($id)
    {
        $material = Material::find($id);
        if (!$material) return back()->with('error', 'Materi tidak ditemukan.');

        try {
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            Http::delete(env('GO_MATERI_URL') . "/api/materials/" . $id);
            $material->delete();
            return back()->with('success', 'Materi telah dihapus secara permanen.');
        } catch (\Exception $e) {
            $material->delete();
            return back()->with('success', 'Materi dihapus lokal (Server Go Offline).');
        }
    }
}