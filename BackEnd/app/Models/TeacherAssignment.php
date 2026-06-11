<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    protected $table = 'teacher_assignments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'class_id',
        'subject_id',
        'subject_name'  // ✅ TAMBAHKAN
    ];

    /**
     * Relasi ke User (Pengajar)
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    /**
     * Relasi ke Class (Program Kelas)
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
