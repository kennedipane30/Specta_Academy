<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $primaryKey = 'promotionsID'; // Sesuai ERD

    protected $fillable = [
        'class_id',
        'image_banner',
        'code',
        'discount_percent',
        'start_date',
        'end_date',
        'is_active'
    ];

    /**
     * RELASI INI YANG TADI HILANG (WAJIB ADA)
     */
    public function classModel()
    {
        // Menghubungkan class_id di tabel promotions ke class_modelsID di tabel class_models
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_modelsID');
    }
}
