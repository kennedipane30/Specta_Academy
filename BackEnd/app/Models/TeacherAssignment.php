<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    protected $table = 'teacher_assignments';
    
    // Pastikan kolom ini sesuai dengan database Anda
    protected $fillable = ['user_id', 'class_id', 'subject_id'];

    /**
     * Relasi ke User (Pengajar)
     * Menggunakan 'usersID' sebagai primary key di tabel users
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    /**
     * Relasi ke Subject (Mata Pelajaran)
     * ✨ PENTING: Inilah yang dipanggil oleh with('subject') di Controller
     */
    public function subject()
    {
        // Parameter: Model, Foreign Key di tabel ini, Primary Key di tabel subjects
        return $this->belongsTo(Subject::class, 'subject_id', 'subject_id');
    }

    /**
     * Relasi ke Class (Program Kelas)
     */
    public function classModel()
    {
        // Parameter: Model, Foreign Key di tabel ini, Primary Key di tabel classes
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}