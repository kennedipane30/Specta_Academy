<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model
{
    // ✨ HUBUNGKAN KE DATABASE TRYOUT
    protected $connection = 'pgsql_tryout';

    protected $table = 'tryout_results';
    protected $primaryKey = 'tryout_result_id';


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
