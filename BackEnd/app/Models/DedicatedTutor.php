<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DedicatedTutor extends Model {
    protected $primaryKey = 'dedicated_tutorsID';
    protected $fillable = ['student_id', 'teacher_id', 'material_id', 'date', 'time', 'status'];

    public function student() { return $this->belongsTo(Student::class, 'student_id', 'studentsID'); }
    public function teacher() { return $this->belongsTo(User::class, 'teacher_id', 'usersID'); }
    public function material() { return $this->belongsTo(Material::class, 'material_id', 'materialsID'); }
}
