<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $primaryKey = 'class_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'program_name',
        'image',
        'price',
        'description',
        'image_url',
        // expires_at → dihapus, tidak dipakai di kelas
    ];

    protected $casts = [
        'price'    => 'integer',
        'class_id' => 'integer',
    ];

    // ─────────────────────────────────────────
    // RELASI
    // ─────────────────────────────────────────

    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class, 'class_id', 'class_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id', 'class_id');
    }
}