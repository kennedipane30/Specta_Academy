<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    use HasFactory;

    protected $table = 'attendances';
    protected $primaryKey = 'attendance_id';

    // ✨ TAMBAHKAN week dan subject_name
    protected $fillable = [
        'user_id',
        'teacher_id',
        'class_id',
        'subject_name',
        'week',
        'status',
        'date'
    ];

public function user() {
    return $this->belongsTo(User::class, 'user_id', 'usersID');
}

    // Relasi ke kelas untuk mempermudah pemanggilan nama program
    public function classModel() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
