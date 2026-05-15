<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Schedule, ClassModel, User};
use Illuminate\Http\Request;

class JadwalController extends Controller {

    public function index() {
        // Memanggil relasi 'class' dan 'teacher' yang sudah dibuat di Model
        $jadwal = Schedule::with(['class', 'teacher'])->latest()->get();
        $classes = ClassModel::all();
        $teachers = User::where('role_id', 2)->get();

        return view('admin.jadwal.index', compact('jadwal', 'classes', 'teachers'));
    }

    public function store(Request $request) {
        $request->validate([
            'class_id' => 'required',
            'teacher_id' => 'required',
            'title' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        // Simpan data (location tidak ikut karena sudah dihapus di migration & model)
        Schedule::create($request->all());

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function destroy($id) {
        Schedule::findOrFail($id)->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal dihapus!');
    }

    public function edit($id) {
        $item = Schedule::findOrFail($id);
        $classes = ClassModel::all();
        $teachers = User::where('role_id', 2)->get();
        return view('admin.jadwal.edit', compact('item', 'classes', 'teachers'));
    }

    public function update(Request $request, $id) {
        $item = Schedule::findOrFail($id);
        $item->update($request->all());
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal diperbarui!');
    }

    public function getMateri($class_id) {
        $materi = \App\Models\Material::where('class_id', $class_id)
                    ->select('title')
                    ->distinct()
                    ->get();

        return response()->json($materi);
    }
}
