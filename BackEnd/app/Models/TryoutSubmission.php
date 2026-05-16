<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryoutSubmission extends Model
{
    protected $table = 'tryout_submissions';

    protected $connection = 'pgsql_tryout';


    protected $fillable = [
        'user_id', 'class_id', 'subject_name',
        'question', 'question_image',
        'option_a', 'option_a_image',
        'option_b', 'option_b_image',
        'option_c', 'option_c_image',
        'option_d', 'option_d_image',
        'correct_answer', 'explanation'
    ];

    // Relasi ke User (Pengajar) menggunakan usersID
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    // Relasi ke Kelas
    public function classModel() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
