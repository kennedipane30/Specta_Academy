<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryoutSubmission extends Model
{
    protected $primaryKey = 'submission_id';
    protected $table = 'tryout_submissions';
    
    protected $fillable = [
        'tryout_id', 'user_id', 'answers', 'score', 'duration_seconds', 'submitted_at'
    ];
    
    protected $casts = [
        'answers' => 'array',
        'submitted_at' => 'datetime'
    ];
    
    public function tryout()
    {
        return $this->belongsTo(Tryout::class, 'tryout_id', 'tryout_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}