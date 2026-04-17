<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model {
    protected $table = 'question_banks';
    protected $primaryKey = 'question_bank_id';
    protected $fillable = ['user_id', 'title', 'subject', 'file_path'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}
