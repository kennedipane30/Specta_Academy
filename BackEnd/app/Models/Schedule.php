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
        'subject_id',
        'teacher_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'meeting_link',
        'status'
    ];

    /**
     * Relasi ke Mata Pelajaran (Model Subject)
     */
    public function subject()
    {
        /**
         * MODIFIKASI PENTING:
         * Parameter ke-2: 'subject_id' adalah kolom Foreign Key di tabel schedules.
         * Parameter ke-3: 'material_id' adalah kolom Primary Key di tabel materials (milik Model Subject).
         */
        return $this->belongsTo(Subject::class, 'subject_id', 'material_id');
    }

    // Relasi ke Pengajar
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }

    // Relasi ke Kelas
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
