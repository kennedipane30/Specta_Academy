<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model {
    protected $fillable = ['user_id', 'class_id', 'subject_name'];

    public function classModel() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
