<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DedicatedTutor extends Model
{
    protected $table = 'dedicated_tutors';
    protected $primaryKey = 'id'; // Sesuai migration: $table->id()

    protected $fillable = [
        'student_id',
        'teacher_id',
        'material_id',
        'date',
        'time',
        'status',
    ];

    // Relasi ke Student (Siswa yang daftar)
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'studentsID');
    }

    // Relasi ke User (Guru yang ditugaskan)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }

    // Relasi ke Material
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'materialsID');
    }
}
