<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Material extends Model {
    protected $primaryKey = 'materialsID'; // Sesuai ERD
    protected $fillable = ['class_id', 'title'];
}
