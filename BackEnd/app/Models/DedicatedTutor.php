<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DedicatedTutor extends Model
{
    protected $table = 'dedicated_tutors';

    // PERBAIKAN UTAMA: Beritahu Laravel nama Primary Key yang benar
    protected $primaryKey = 'dedicated_tutor_id';

    // Jika Primary Key Anda bukan 'id', disarankan tambahkan ini juga
    public $incrementing = true;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'material_id',
        'date',
        'time',
        'status'
    ];

    /**
     * RELATIONS
     */

    public function student()
    {
        // student_id di dedicated_tutors merujuk ke student_id di tabel students
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function material()
    {
        // material_id merujuk ke material_id di tabel materials
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function teacher()
    {
        // teacher_id merujuk ke usersID di tabel users
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }
}
