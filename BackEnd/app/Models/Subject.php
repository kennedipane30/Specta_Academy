<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    // Arahkan ke tabel materials karena tabel subjects tidak digunakan
    protected $table = 'materials';
    protected $primaryKey = 'material_id';

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
        // Hubungkan subject_id di penugasan ke material_id di tabel ini
        return $this->hasMany(TeacherAssignment::class, 'subject_id', 'material_id');
    }
}
