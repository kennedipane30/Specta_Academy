<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'student_id'; // Sesuai pgAdmin

    protected $fillable = [
        'user_id',
        'class_id',
        'parent_name',
        'address',
        'parent_phone',
        'national_id_number', // Key ini yang akan dikirim ke Flutter
        'date_of_birth'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
