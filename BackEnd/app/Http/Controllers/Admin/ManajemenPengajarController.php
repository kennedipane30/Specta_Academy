<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManajemenPengajarController extends Controller
{
    public function index()
    {
        // Ambil user yang rolenya Pengajar (role_id = 2)
        // Variabel diubah menjadi $teachers agar sesuai dengan @foreach di Blade
        $teachers = User::where('role_id', 2)->latest()->get();

        // PENTING: Folder di resources/views/admin/pengajar/ maka panggil admin.pengajar.index
        return view('admin.pengajar.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required',
            'password' => 'required|min:6',
        ]);

        // 2. Simpan Data ke Tabel Users
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password), // Enkripsi password
            'role_id'  => 2, // Set otomatis sebagai Pengajar
        ]);

        return redirect()->back()->with('success', 'Akun Pengajar berhasil didaftarkan!');
    }

    public function destroy($id)
    {
        // Menghapus data berdasarkan Primary Key (usersID)
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Akun Pengajar berhasil dihapus!');
    }
}
