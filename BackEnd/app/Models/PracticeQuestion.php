<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeQuestion extends Model
{
protected $connection = 'pgsql_practice';
    protected $table = 'practice_questions';
    protected $primaryKey = 'practice_question_id';

    protected $fillable = [
        'class_id', 'subject', 'week', 'question', 'option_a', 'option_b',
        'option_c', 'option_d', 'correct_answer', 'explanation'
    ];
}
