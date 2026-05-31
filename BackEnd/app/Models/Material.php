<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    // Pastikan koneksi ini sudah terdaftar di config/database.php
    protected $connection = 'pgsql_materi';

    // Nama tabel di database specta_materi
    protected $table = 'materials';

    // 🔥 PENTING: Tentukan Primary Key Anda
    protected $primaryKey = 'material_id';

    // 🔥 PENTING: Beritahu Laravel bahwa ini adalah auto-incrementing
    public $incrementing = true;

    // 🔥 Tipe data primary key (biasanya int atau bigInt)
    protected $keyType = 'int';

    // Kolom yang boleh diisi
    protected $fillable = [
        'class_id', 
        'user_id', 
        'title', 
        'material_name', 
        'file_path', 
        'week'
    ];

    /**
     * Relasi ke Model ClassModel
     * Pastikan ClassModel menggunakan koneksi default (auth/main db)
     */
    public function classModel()
    {
        // Gunakan nama classModel agar tidak bentrok dengan keyword 'class' di PHP lama
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Relasi ke Pengajar (User)
     */
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}