<?php
namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MateriController extends Controller {

    public function index()
    {
        $assignments = TeacherAssignment::with('classModel')
                        ->where('user_id', Auth::user()->usersID)
                        ->get();

        return view('pengajar.materi.index', compact('assignments'));
    }

    public function pilihMateri($class_id, $subject_name) {
        $access = TeacherAssignment::where('user_id', Auth::user()->usersID)
                    ->where('class_id', $class_id)
                    ->where('subject_name', $subject_name)
                    ->first();

        if (!$access) abort(403, 'Akses Ditolak!');

        $class = ClassModel::findOrFail($class_id);

        $materis = Material::where('class_id', $class_id)
                    ->where('material_name', $subject_name)
                    ->orderBy('week', 'asc')->get();

        return view('pengajar.materi.pilih', compact('class', 'materis', 'subject_name'));
    }

    public function store(Request $request, $class_id) {
        $request->validate([
            'title'         => 'required',
            'material_name' => 'required',
            'file_pdf'      => 'required|mimes:pdf|max:10240',
            'week'          => 'required|integer|min:1|max:20',
        ]);

        $path = $request->file('file_pdf')->store('materi', 'public');

        $material = Material::create([
            'class_id'      => $class_id,
            'user_id'       => Auth::user()->usersID,
            'title'         => $request->title,
            'material_name' => $request->material_name,
            'week'          => $request->week,
            'file_path'     => $path,
        ]);

        // ✨ MODIFIKASI: Kirim ke Port 9001 (Materi Service)
        try {
            $response = Http::post(env('GO_MATERI_URL') . '/materials/sync', [
                'material_id'   => $material->material_id,
                'class_id'      => (int)$class_id,
                'user_id'       => (int)Auth::user()->usersID,
                'title'         => $request->title,
                'material_name' => $request->material_name,
                'week'          => (int)$request->week,
                'file_path'     => $path,
            ]);

            if (!$response->successful()) {
                Log::error("Materi Service (9001) gagal merespon: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Koneksi ke Materi Service (9001) terputus.");
        }

        return back()->with('success', 'Materi berhasil diunggah dan disinkronkan ke Service Materi!');
    }
}
