<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'announcement_id';

    protected $fillable = [
        'title',
        'description',
        'image'
    ];

    // ✅ Tambahkan accessor untuk URL gambar lengkap
    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // Jika sudah full URL, return langsung
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Hapus leading slash jika ada
        $path = ltrim($this->image, '/');

        // Jika path sudah dimulai dengan storage/, gunakan asset
        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // Default: simpan di folder announcements
        return asset('storage/announcements/' . basename($path));
    }
}
