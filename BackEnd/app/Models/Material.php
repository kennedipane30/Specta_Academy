<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    // Nama tabel di database
    protected $table = 'materials';

    // Primary key diubah ke bahasa Inggris (sebelumnya: materialsID)
    protected $primaryKey = 'material_id';

    // Atribut fillable diubah ke bahasa Inggris
    protected $fillable = [
        'class_id',
        'title',
        'material_name', // Sebelumnya: nama_materi
        'file_path',
        'week'           // Sebelumnya: minggu
    ];

    public function class()
    {
        return $this->belongsTo(\App\Models\ClassModel::class, 'class_id');
    }
}

