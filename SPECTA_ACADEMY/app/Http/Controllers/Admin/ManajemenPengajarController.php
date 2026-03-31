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
        // Ambil semua user dengan Role Pengajar (RoleID 2)
        $teachers = User::where('role_id', 2)->latest()->get();
        return view('admin.pengajar.index', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 2, // OTOMATIS JADI PENGAJAR
        ]);

        return back()->with('success', 'Akun Pengajar berhasil dibuat!');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'Akun Pengajar berhasil dihapus!');
    }
}
