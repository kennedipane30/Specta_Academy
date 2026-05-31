<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'program_name',
        'image',        // Nama file/path di server
        'price',        // Harga program
        'description',  // Detail program
        'image_url'     // URL lengkap untuk Flutter
    ];

    /**
     * 🔥 MODIFIKASI PENTING:
     * Mengonversi tipe data secara otomatis saat model diakses.
     * Ini memastikan 'price' selalu menjadi angka (integer) di API Flutter,
     * sehingga tidak muncul sebagai Rp 0.
     */
    protected $casts = [
        'price' => 'integer',
        'class_id' => 'integer',
    ];

    /**
     * Relasi ke Tabel Materials
     */
    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class, 'class_id', 'class_id');
    }

    /**
     * Relasi ke Tabel Enrollments (Pendaftaran Siswa)
     * Tambahan jika Anda membutuhkannya nanti
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id', 'class_id');
    }
}