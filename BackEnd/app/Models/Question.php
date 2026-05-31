<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $primaryKey = 'question_id';
    
    protected $table = 'questions';
    
    protected $fillable = [
        'tryout_id',
        'class_id',       // ✨ TAMBAHKAN INI
        'subject',
        'question_number',
        'question',
        'question_image',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',       // ✨ TAMBAHKAN INI
        'option_a_image',
        'option_b_image',
        'option_c_image',
        'option_d_image',
        'option_e_image',      // ✨ TAMBAHKAN INI
        'correct_answer',
        'explanation',
        'points'
    ];
    
    // Relationship
    public function tryout()
    {
        return $this->belongsTo(Tryout::class, 'tryout_id', 'tryout_id');
    }

    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}