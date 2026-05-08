<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'program_name',
        'image',        // Kolom wajib di DB Anda
        'price',
        'description',
        'image_url'     // Kolom untuk Flutter
    ];

    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class, 'class_id');
    }
}
