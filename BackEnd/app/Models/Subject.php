<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    // 1. Ubah ke tabel 'materials' karena tabel 'subjects' tidak ada di database Anda
    protected $table = 'materials';
    protected $primaryKey = 'material_id';

    // 2. Sesuaikan fillable dengan kolom di tabel materials
    protected $fillable = ['material_name', 'class_id'];

    /**
     * Agar kode lain yang memanggil $subject->name tetap jalan,
     * kita buat "alias" dari material_name ke name.
     */
    public function getNameAttribute()
    {
        return $this->material_name;
    }

    public function assignments()
    {
        // Parameter ke-3 merujuk ke primary key tabel materials
        return $this->hasMany(TeacherAssignment::class, 'subject_id', 'material_id');
    }
}
