<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tryout extends Model
{
    protected $primaryKey = 'tryoutsID';

    protected $fillable = [
        'class_id',
        'title',
        'duration' // <--- PASTIKAN ADA BARIS INI
    ];

    public function questions() {
        return $this->hasMany(Question::class, 'tryout_id', 'tryoutsID');
    }
}
