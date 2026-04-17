<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = true;

    protected $fillable = [
        'program_name',
        'image',
        'price'
    ];

    // ✅ PINDAHKAN KE DALAM CLASS
    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class, 'class_id');
    }
}