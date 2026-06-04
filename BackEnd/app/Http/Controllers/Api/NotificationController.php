<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Mengambil semua daftar notifikasi milik user yang sedang login.
     * Digunakan untuk menampilkan list di halaman Notifikasi Flutter.
     */
    public function index(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()->notifications 
        ]);
    }

    /**
     * Menghitung jumlah notifikasi yang belum dibaca.
     * Digunakan untuk menampilkan angka merah (badge) pada ikon lonceng di Home.
     */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'unread_count' => $request->user()->unreadNotifications->count()
        ]);
    }

    /**
     * Menandai semua notifikasi sebagai "Sudah Dibaca".
     * Biasanya dipanggil saat user membuka halaman daftar notifikasi.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Semua notifikasi telah ditandai sebagai sudah dibaca'
        ]);
    }

    /**
     * Menandai satu notifikasi tertentu sebagai "Sudah Dibaca".
     * @param string $id (UUID notifikasi)
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'status' => 'success', 
                'message' => 'Notifikasi berhasil ditandai dibaca'
            ]);
        }

        return response()->json([
            'status' => 'error', 
            'message' => 'Notifikasi tidak ditemukan'
        ], 404);
    }
}