<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model {
protected $primaryKey = 'studentsID';
protected $fillable = ['user_id', 'parent_name', 'school', 'wa_ortu', 'nisn', 'dob', 'grade'];
}
