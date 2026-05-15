<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model {
    protected $fillable = ['user_id', 'class_id', 'subject_name'];

    // ✨ TAMBAHKAN INI UNTUK MEMPERBAIKI ERROR
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    public function classModel() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
