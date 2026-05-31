<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TryoutDraft extends Model
{
    use HasFactory;

    protected $table = 'tryout_drafts';

    protected $fillable = [
        'class_id', 
        'user_id', 
        'subject_name', 
        'question', 
        'option_a', 
        'option_b', 
        'option_c', 
        'option_d', 
        'option_e', 
        'correct_answer', 
        'explanation'
    ];

    // Relasi ke Guru Pengirim
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}