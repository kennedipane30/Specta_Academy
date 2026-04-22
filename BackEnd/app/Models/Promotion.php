<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';
    protected $primaryKey = 'promotion_id';

    protected $fillable = [
        'class_id', 
        'code', 
        'discount_type', 
        'discount_percent', // Nama kolom di database Anda
        'quota', 
        'start_date', 
        'end_date',
        'is_active',
        'image_banner'
    ];

    public function class()
    {
        // Relasi ke ClassModel
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}