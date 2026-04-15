<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';

    // SESUAIKAN DENGAN PGADMIN: PK adalah announcement_id
    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'title',
        'description',
        'image'
    ];
}
