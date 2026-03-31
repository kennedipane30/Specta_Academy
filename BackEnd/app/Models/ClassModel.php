<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    // MODIFIKASI: Beritahu Laravel nama PK sesuai ERD
    protected $primaryKey = 'class_modelsID';

    protected $fillable = [
        'nama_program',
        'gambar'
    ];
}
