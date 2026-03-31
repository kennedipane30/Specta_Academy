<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Schedule, ClassModel, User};
use Illuminate\Http\Request;

class JadwalController extends Controller {
    public function index() {
        $jadwal = Schedule::with(['classModel', 'teacher'])->latest()->get();
        $classes = ClassModel::all();
        $teachers = User::where('role_id', 2)->get(); // Hanya ambil Pengajar
        return view('admin.jadwal.index', compact('jadwal', 'classes', 'teachers'));
    }

    public function store(Request $request) {
        $request->validate([
            'class_id' => 'required', 'teacher_id' => 'required',
            'title' => 'required', 'date' => 'required|date',
            'start_time' => 'required', 'end_time' => 'required',
        ]);

        Schedule::create($request->all());
        return back()->with('success', 'Jadwal berhasil ditambahkan!');
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

    public function destroy($id) {
        Schedule::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal dihapus!');
    }
}
