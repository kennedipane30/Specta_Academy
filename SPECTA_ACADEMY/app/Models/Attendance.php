<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    // 1. MODIFIKASI: Beritahu Laravel nama PK sesuai ERD kamu
    protected $primaryKey = 'attendancesID';

    // 2. MODIFIKASI: Izinkan kolom-kolom ini diisi secara massal (PENTING!)
    protected $fillable = [
        'schedule_id',
        'user_id',
        'status',
        'date'
    ];

    /**
     * Relasi ke Jadwal
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'schedulesID');
    }

    /**
     * Relasi ke Siswa (User)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}
