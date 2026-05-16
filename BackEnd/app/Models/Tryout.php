<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tryout extends Model
{
protected $connection = 'pgsql_tryout';
    protected $table = 'tryouts';
    protected $primaryKey = 'tryout_id';

    protected $fillable = ['class_id', 'title', 'duration', 'is_active'];

    public function questions() {
        return $this->hasMany(Question::class, 'tryout_id', 'tryout_id');
    }
}
