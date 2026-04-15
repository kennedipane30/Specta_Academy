<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    // MODIFIKASI: Gunakan primary key bahasa Inggris sesuai migrasi terakhir
    protected $primaryKey = 'enrollment_id';

    protected $fillable = [
        'user_id',
        'class_id',
        'payment_proof',
        'status',
        'expires_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    // MODIFIKASI: Nama fungsi harus 'class' agar sinkron dengan Controller Admin
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
