<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    // Jika di ERD kamu PK-nya galleriesID, aktifkan baris bawah ini:
    // protected $primaryKey = 'galleriesID';

    protected $fillable = [
        'judul',
        'foto',
        'deskripsi' // <--- PASTIKAN ADA DI SINI
    ];
}
