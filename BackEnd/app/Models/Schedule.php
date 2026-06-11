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
        'status'
    ];

    /**
     * Relasi ke Pengajar (User)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }

    /**
     * Relasi ke Kelas
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Accessor untuk mendapatkan nama mata pelajaran dari teacher_assignments
     * (Karena tidak ada foreign key, ambil dari relasi tidak langsung)
     */
    public function getSubjectNameAttribute()
    {
        $assignment = TeacherAssignment::where('class_id', $this->class_id)
            ->where('subject_id', $this->subject_id)
            ->first();

        return $assignment ? $assignment->subject_name : $this->title;
    }
}
