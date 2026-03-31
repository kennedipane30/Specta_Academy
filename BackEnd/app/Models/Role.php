<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // MODIFIKASI: Beritahu Laravel nama PK sesuai ERD
    protected $primaryKey = 'rolesID';

    protected $fillable = ['name'];
}
