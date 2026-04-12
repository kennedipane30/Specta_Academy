<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DedicatedTutor extends Model
{
    use HasFactory;

    protected $table = 'dedicated_tutors';
    // Pastikan primary key sesuai dengan migrasi Anda
    protected $primaryKey = 'id';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'material_id',
        'date',
        'time',
        'status',
    ];

    // Relasi ke Model Student (Siswa yang request)
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'studentsID');
    }

    // Relasi ke User (Sebagai Pengajar yang ditugaskan Admin)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'userID');
    }

    // Relasi ke Material
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'materialsID');
    }
}
