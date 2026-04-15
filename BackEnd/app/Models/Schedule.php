<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // MODIFIKASI: Menggunakan Primary Key bahasa Inggris sesuai migrasi
    protected $primaryKey = 'schedule_id';

    // Kolom yang dapat diisi (fillable)
    protected $fillable = [
        'class_id',
        'teacher_id',
        'title',
        'date',
        'start_time',
        'end_time'
    ];

    /**
     * RELATIONS
     */

    // Relasi ke Program/Kelas
    public function class()
    {
        // 'class_id' di tabel schedules merujuk ke 'class_id' di tabel classes
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    // Relasi ke Pengajar/User
    public function teacher()
    {
        /**
         * 'teacher_id' di tabel schedules merujuk ke 'usersID' di tabel users
         * (Disesuaikan karena migrasi user tidak diubah)
         */
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }
}
