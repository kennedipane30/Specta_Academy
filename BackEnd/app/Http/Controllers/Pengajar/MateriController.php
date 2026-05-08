<?php
namespace App\Http\Controllers\Pengajar;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Material;
use App\Models\TeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller {
    public function index()
    {
        // Mengambil penugasan materi milik pengajar yang sedang login
        $assignments = TeacherAssignment::with('classModel')
                        ->where('user_id', Auth::user()->usersID)
                        ->get();

        // Kirim variabel $assignments ke view
        return view('pengajar.materi.index', compact('assignments'));
    }

    public function pilihMateri($class_id, $subject_name) {
        // VALIDASI: Cek apakah pengajar ini ditugaskan di subjek ini
        $access = TeacherAssignment::where('user_id', Auth::user()->usersID)
                    ->where('class_id', $class_id)
                    ->where('subject_name', $subject_name)
                    ->first();

        if (!$access) abort(403, 'Akses Ditolak! Anda bukan pengajar untuk materi ini.');

        $class = ClassModel::findOrFail($class_id);
        // Tampilkan 20 minggu yang sudah terupload untuk subjek ini
        $materis = Material::where('class_id', $class_id)
                    ->where('material_name', $subject_name)
                    ->orderBy('week', 'asc')->get();

        return view('pengajar.materi.pilih', compact('class', 'materis', 'subject_name'));
    }

    public function store(Request $request, $class_id) {
        $request->validate([
            'title'         => 'required',
            'material_name' => 'required', // Ini otomatis berisi subjek miliknya (TIU/TWK/dll)
            'file_pdf'      => 'required|mimes:pdf|max:10240',
            'week'          => 'required|integer|min:1|max:20',
        ]);

        $path = $request->file('file_pdf')->store('materi', 'public');

        Material::create([
            'class_id'      => $class_id,
            'title'         => $request->title,
            'material_name' => $request->material_name,
            'week'          => $request->week,
            'file_path'     => $path,
        ]);

        return back()->with('success', 'Materi Minggu ke-' . $request->week . ' berhasil diunggah!');
    }
}
