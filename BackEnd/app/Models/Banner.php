<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'image',
        'link',
        'is_active',
        'order_position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
    ];

    /**
     * 🔥 KONTROL AKSES URL GAMBAR DINAMIS (ANTI-PECAH)
     */
    public function getImageUrlAttribute(): string
    {
        // 1. Jika data image di database kosong, kembalikan string kosong atau gambar default
        if (!$this->image) {
            return '';
        }

        // 2. Ambil URL default dari konfigurasi Storage Laravel
        $url = Storage::url($this->image);

        // 3. Ambil User-Agent dari request untuk mendeteksi siapa yang mengakses API
        $userAgent = request()->header('User-Agent', '');

        // 4. JIKA yang meminta data adalah EMULATOR ANDROID (Flutter Mobile):
        //    Ubah paksa domain localhost/127.0.0.1 menjadi IP khusus emulator (10.0.2.2)
        if (str_contains(strtolower($userAgent), 'android') || str_contains(strtolower($userAgent), 'flutter')) {
            $url = str_replace('127.0.0.1', '10.0.2.2', $url);
            $url = str_replace('localhost', '10.0.2.2', $url);
        }
        // 5. JIKA yang meminta adalah BROWSER LAPTOP (Web Admin):
        //    Pastikan link-nya mengarah ke localhost/127.0.0.1 agar gambar tidak pecah di web
        else {
            $url = str_replace('10.0.2.2', '127.0.0.1', $url);
        }

        return $url;
    }
}
