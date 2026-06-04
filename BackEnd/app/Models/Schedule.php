<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id'; // Menghilangkan error "column id does not exist"

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'meeting_link', // Sudah ditambahkan
        'status'
    ];

    // Relasi ke Mata Pelajaran
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
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