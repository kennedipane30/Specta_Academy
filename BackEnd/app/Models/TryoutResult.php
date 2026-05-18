<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model
{
    // Tetap gunakan database microservice
    protected $connection = 'pgsql_tryout';
    protected $table = 'tryout_results';
    protected $primaryKey = 'tryout_result_id';

    protected $fillable = [
        'user_id',
        'tryout_id',
        'score',
        'total_correct'
    ];

    // ⚠️ JANGAN tambahkan relasi user() di sini karena akan menyebabkan error "table not exist"
}
