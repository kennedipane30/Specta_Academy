<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
protected $connection = 'pgsql_materi';
    protected $table = 'materials';
    protected $primaryKey = 'material_id';

    protected $fillable = [
        'class_id', 'user_id', 'title', 'material_name', 'file_path', 'week'
    ];

    public function class() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
