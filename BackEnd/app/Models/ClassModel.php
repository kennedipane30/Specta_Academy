<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'program_name',
        'image',
        'price',
        'description',
        'image_url',
    ];

    protected $casts = [
        'price'    => 'integer',
        'class_id' => 'integer',
    ];

    // ─────────────────────────────────────────
    // RELASI
    // ─────────────────────────────────────────

    /**
     * ❌ HAPUS METHOD materials() KARENA TABEL materials SUDAH DIHAPUS
     * Data materi sekarang diambil dari Microservice
     */
    // public function materials() { ... } // HAPUS!

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id', 'class_id');
    }

    /**
     * Relasi ke TeacherAssignment (penugasan pengajar)
     */
    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'class_id', 'class_id');
    }
}
