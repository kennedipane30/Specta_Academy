<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    // Primary Key
    protected $primaryKey = 'student_id';

    protected $fillable = [
        'user_id',
        'class_id',
        'parent_name',
        'address',             // Sebelumnya: school / alamat
        'parent_phone',        // Sebelumnya: wa_ortu
        'national_id_number', // Sebelumnya: nisn
        'date_of_birth'       // Sebelumnya: dob
    ];

    /**
     * RELATIONSHIPS
     */

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi ke Class
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
