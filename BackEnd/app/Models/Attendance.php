<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    // Pastikan fillable sesuai dengan kolom di database Anda
    protected $fillable = ['schedule_id', 'user_id', 'status', 'date'];

    /**
     * Relasi ke Model User (Siswa)
     */
    public function user()
    {
        // Sesuaikan 'user_id' (foreign key di tabel attendances)
        // dan 'usersID' (primary key di tabel users sesuai kode Anda sebelumnya)
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}
