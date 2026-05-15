<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'class_id',
        'teacher_id',
        'title',
        'date',
        'start_time',
        'end_time'
        // 'location' sudah dihapus sesuai permintaan sebelumnya
    ];

    /**
     * Relasi ke ClassModel
     * Nama fungsi 'class' harus sama dengan yang dipanggil di Controller with(['class'])
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Relasi ke User (Pengajar)
     * teacher_id merujuk ke usersID di tabel users
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }
}
