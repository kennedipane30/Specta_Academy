<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    // Nama tabel diubah menjadi 'classes'
    protected $table = 'classes';

    // Nama Primary Key diubah menjadi 'class_id'
    protected $primaryKey = 'class_id';

    // Atribut diubah ke Bahasa Inggris
    protected $fillable = [
        'program_name',
        'image',
        'price'
    ];
}
