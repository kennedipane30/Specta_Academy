<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DedicatedTutor extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'dedicated_tutors';

    // Mendefinisikan Primary Key secara spesifik
    protected $primaryKey = 'dedicated_tutor_id';

    // Memberitahu Laravel bahwa primary key bukan 'id' dan bertipe auto-increment
    public $incrementing = true;
    protected $keyType = 'int';

    // Kolom yang boleh diisi secara mass-assignment
    protected $fillable = [
        'student_id',
        'teacher_id',
        'material_id',
        'date',
        'time',
        'status'
    ];

    /**
     * RELASI: Menghubungkan ke tabel Material
     * Digunakan untuk mengambil nama topik (title) di aplikasi Flutter
     */
    public function material()
    {
        // material_id di tabel ini merujuk ke material_id di tabel materials
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    /**
     * RELASI: Menghubungkan ke tabel Student
     * Digunakan untuk identifikasi siswa yang melakukan pengajuan
     */
    public function student()
    {
        // student_id di tabel ini merujuk ke student_id di tabel students
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * RELASI: Menghubungkan ke tabel User (sebagai Pengajar)
     * Digunakan untuk melihat siapa tutor yang dikonfirmasi oleh admin
     */
    public function teacher()
    {
        // teacher_id di tabel ini merujuk ke usersID di tabel users
        return $this->belongsTo(User::class, 'teacher_id', 'usersID');
    }

    /**
     * CASTING: Memastikan tipe data konsisten saat dikirim ke API
     */
    protected $casts = [
        'date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}