<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model
{
    protected $table = 'tryout_results';
    protected $primaryKey = 'result_id'; // ✨ Sesuaikan dengan SQL di atas

    protected $fillable = [
        'user_id', 
        'tryout_id', 
        'score', 
        'total_correct'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}