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
        $teachers = User::where('role_id', 2)->latest()->get();
        return view('admin.pengajar.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role_id'  => 2,
        ]);

        return redirect()->back()->with('success', 'Akun Pengajar berhasil didaftarkan!');
    }

    public function destroy($id)
    {
        // PK usersID tetap digunakan sesuai struktur User yang tidak diubah
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'Akun Pengajar berhasil dihapus!');
    }
}
