<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Student extends Model {
    protected $primaryKey = 'studentsID';
    // Tambahkan 'class_id' di sini
    protected $fillable = ['user_id', 'class_id', 'parent_name', 'school', 'wa_ortu', 'nisn', 'dob'];
}
