<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // MODIFIKASI: Gunakan question_id sesuai pgAdmin
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'tryout_id',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation'
    ];
}
