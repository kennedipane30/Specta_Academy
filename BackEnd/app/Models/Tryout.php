<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tryout extends Model
{
    // MODIFIKASI: Gunakan tryout_id sesuai pgAdmin
    protected $primaryKey = 'tryout_id';

    protected $fillable = [
        'class_id',
        'title',
        'duration'
    ];

    public function questions() {
        // Relasi ke Model Question menggunakan foreign key tryout_id
        return $this->hasMany(Question::class, 'tryout_id', 'tryout_id');
    }
}
