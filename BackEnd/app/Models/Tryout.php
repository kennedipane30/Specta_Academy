<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tryout extends Model
{
    protected $table = 'tryouts';
    protected $primaryKey = 'tryout_id';

    protected $fillable = ['class_id', 'title', 'duration'];

    public function questions() {
        return $this->hasMany(Question::class, 'tryout_id', 'tryout_id');
    }

    // TAMBAHKAN INI: Relasi ke tabel hasil nilai
    public function results() {
        return $this->hasMany(TryoutResult::class, 'tryout_id', 'tryout_id');
    }

    // Relasi ke Program Kelas
    public function class() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
