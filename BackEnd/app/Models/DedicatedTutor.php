<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DedicatedTutor extends Model
{
    use HasFactory;

    protected $table = 'dedicated_tutors';
    protected $primaryKey = 'dedicated_tutor_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'material_id',  // Hanya sebagai penyimpan ID, tanpa relasi
        'date',
        'time',
        'status'
    ];

    // ❌ HAPUS relasi material() karena tabel materials sudah dihapus
    // public function material() { ... }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }

    protected $casts = [
        'date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
