<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionUsage extends Model
{
protected $fillable = ['promotion_id', 'student_id', 'enrollment_id'];
}
