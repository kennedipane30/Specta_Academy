<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Question extends Model {
    protected $primaryKey = 'questionsID';
    protected $fillable = ['tryout_id', 'question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'explanation'];
}
