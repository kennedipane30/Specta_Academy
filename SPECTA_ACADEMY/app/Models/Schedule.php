<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $primaryKey = 'schedulesID';
protected $fillable = ['class_id', 'teacher_id', 'title', 'date', 'start_time', 'end_time'];

public function classModel() { return $this->belongsTo(ClassModel::class, 'class_id'); }
public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
}
