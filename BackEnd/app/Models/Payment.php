<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'user_id',
        'class_id',
        'order_id',
        'amount',
        'status',
        'snap_token',
        'payment_type'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    public function class() {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}