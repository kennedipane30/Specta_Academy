<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    /**
     * Menampilkan Halaman Login
     * Jika user sudah login, otomatis diarahkan ke dashboard masing-masing
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * Proses Autentikasi Login Website
     */
    public function login(Request $request)
    {
        // 1. Validasi Input (Syarat Keamanan & Integritas Data)
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Gmail wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // 2. Percobaan Login
        if (Auth::attempt($credentials)) {
            // Mencegah serangan Session Fixation (Syarat Keamanan Perangkat Lunak)
            $request->session()->regenerate();

            return $this->redirectUser(Auth::user());
        }

        // 3. Jika Gagal, kembali ke login dengan pesan error
        return back()->with('error', 'Gmail atau Password yang Anda masukkan salah!');
    }

    /**
     * Logika Pengalihan Berdasarkan Role (RBAC)
     * Admin -> Dashboard Admin
     * Pengajar -> Dashboard Pengajar
     * Siswa -> Ditolak (Hanya boleh di Mobile)
     */
    private function redirectUser($user)
    {
        // Role ID 1 = Administrasi
        if ($user->role_id == 1) {
            return redirect()->intended('/admin/dashboard');
        }

        // Role ID 2 = Pengajar
        if ($user->role_id == 2) {
            return redirect()->intended('/pengajar/dashboard');
        }

        // Jika Siswa (Role ID 3) mencoba masuk ke Web Administrasi
        Auth::logout();
        return redirect('/login')->with('error', 'Akses Ditolak! Siswa Spekta Academy hanya dapat login melalui Aplikasi Mobile.');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Menghapus session agar benar-benar bersih (Security Best Practice)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil keluar.');
    }
}
