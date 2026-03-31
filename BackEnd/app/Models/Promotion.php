<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    // 1. Beritahu Laravel nama Primary Key sesuai ERD kamu
    protected $primaryKey = 'promotionsID';

    // 2. Izinkan kolom ini diisi secara massal
    protected $fillable = [
        'image_banner',
        'code',
        'discount_percent',
        'start_date',
        'end_date',
        'is_active'
    ];

    /**
     * Relasi ke tabel Penggunaan Promo (Jika nanti ingin ditarik datanya)
     */
    public function usages()
    {
        return $this->hasMany(PromotionUsage::class, 'promotion_id', 'promotionsID');
    }
}
