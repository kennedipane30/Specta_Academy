<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
protected $connection = 'pgsql_tryout';
   protected $table = 'questions';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'class_id', 'tryout_id', 'question', 'question_image',
        'option_a', 'option_a_image', 'option_b', 'option_b_image',
        'option_c', 'option_c_image', 'option_d', 'option_d_image',
        'correct_answer', 'explanation'
    ];
}
