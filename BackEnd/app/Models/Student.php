<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    protected $table = 'students';
    protected $primaryKey = 'studentsID';

    protected $fillable = [
        'user_id',
        'class_id',
        'parent_name',
        'school',
        'wa_ortu',
        'nisn',
        'dob'
    ];

    // RELASI UTAMA: Agar bisa panggil $student->user->name
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    public function class_model()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_modelsID');
    }
}
