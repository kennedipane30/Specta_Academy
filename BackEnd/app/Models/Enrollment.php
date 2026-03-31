<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model {
    protected $primaryKey = 'enrollmentsID';
    protected $fillable = ['user_id', 'class_id', 'payment_proof', 'status', 'expires_at'];

    public function user() { return $this->belongsTo(User::class, 'user_id', 'usersID'); }
    public function classModel() { return $this->belongsTo(ClassModel::class, 'class_id', 'class_modelsID'); }
}

