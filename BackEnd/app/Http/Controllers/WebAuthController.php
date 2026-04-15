<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Gmail wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return $this->redirectUser(Auth::user());
        }

        return back()->with('error', 'Gmail atau Password yang Anda masukkan salah!');
    }

    private function redirectUser($user)
    {
        if ($user->role_id == 1) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->role_id == 2) {
            return redirect()->intended('/pengajar/dashboard');
        }

        Auth::logout();
        return redirect('/login')->with('error', 'Akses Ditolak! Siswa hanya dapat login melalui Aplikasi Mobile.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
