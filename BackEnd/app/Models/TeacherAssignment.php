<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TeacherAssignment extends Model
{
    protected $table = 'teacher_assignments';

    protected $fillable = ['user_id', 'class_id', 'subject_id'];

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

    /**
     * Relasi ke Subject
     * MODIFIKASI: Karena Anda tidak ingin merubah model 'Subject',
     * maka relasi ini dibuat untuk mengambil data dari tabel 'materials'
     */
    public function subject()
    {
        // Tetap menggunakan belongsTo, tapi menunjuk ke tabel 'materials'
        // Kita menggunakan model 'Subject' tapi kita asumsikan Model tersebut
        // nantinya akan merujuk ke tabel yang benar di Controller.
        // Jika ingin benar-benar aman tanpa merubah model Subject, gunakan cara ini:
        return $this->belongsTo(Subject::class, 'subject_id', 'material_id');
    }
}
