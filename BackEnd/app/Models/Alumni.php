<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Alumni extends Model {
    protected $primaryKey = 'alumniID';
    protected $fillable = ['nama', 'berhasil_menjadi', 'foto'];
}
