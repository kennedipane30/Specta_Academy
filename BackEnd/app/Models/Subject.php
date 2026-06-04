<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects'; // Nama tabel di database
    protected $primaryKey = 'subject_id';

    protected $fillable = ['name'];

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'subject_id', 'subject_id');
    }
}   