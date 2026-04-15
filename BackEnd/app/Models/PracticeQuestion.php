<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticeQuestion extends Model
{
    // Nama tabel disesuaikan
    protected $table = 'practice_questions';

    // Nama primary key disesuaikan (sebelumnya: latihan_soalID)
    protected $primaryKey = 'practice_question_id';

    // Fillable disesuaikan (struktur urutan tetap sama agar tidak bingung)
    protected $fillable = [
        'class_id',
        'subject',
        'week',
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation'
    ];
}
