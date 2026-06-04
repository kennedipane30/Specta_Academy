<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TryoutResult extends Model
{
    use HasFactory;

    // 1. Nama Tabel (Penting jika Laravel tidak bisa mendeteksi otomatis)
    protected $table = 'tryout_results';

    /**
     * 2. Primary Key
     * ✨ FIX: Laravel secara default mencari kolom 'id'. 
     * Karena di PostgreSQL Anda namanya 'result_id', kita harus mendefinisikannya di sini.
     */
    protected $primaryKey = 'result_id';

    /**
     * 3. Incrementing
     * Beritahu Laravel bahwa primary key ini adalah Auto-Incrementing (Serial di PGSQL).
     */
    public $incrementing = true;

    /**
     * 4. Mass Assignment
     * Kolom-kolom yang diizinkan untuk diisi melalui TryoutResult::create()
     */
    protected $fillable = [
        'user_id', 
        'tryout_id', 
        'score', 
        'total_correct'
    ];

    /**
     * Relasi ke User
     * Menghubungkan 'user_id' di tabel ini dengan 'usersID' di tabel users.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    /**
     * Opsi: Jika tabel Anda tidak memiliki created_at dan updated_at, 
     * hapus komentar baris di bawah ini:
     */
    // public $timestamps = false;
}