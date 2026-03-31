<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // MODIFIKASI: Cek menggunakan properti 'name' sesuai ERD
        if ($request->user()->role->name !== $role) {
            abort(403, 'Akses ditolak!');
        }

        return $next($request);
    }
}
