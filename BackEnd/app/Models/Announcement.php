<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    // Pastikan nama ini SAMA PERSIS dengan yang ada di migration (Langkah 1)
    protected $primaryKey = 'announcementsID';

    protected $fillable = [
        'title',
        'description',
        'image'
    ];
}
