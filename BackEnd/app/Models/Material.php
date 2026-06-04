<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    /**
     * MODIFIKASI PENTING:
     * Karena di file .env Anda hanya ada satu database (db_spectaacademy),
     * kita harus menonaktifkan baris connection di bawah ini.
     * Jika diaktifkan, Laravel akan error karena mencari database 'pgsql_materi' yang tidak ada di .env.
     */
    // protected $connection = 'pgsql_materi'; 

    protected $table = 'materials';
    protected $primaryKey = 'material_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'class_id', 
        'user_id', 
        'title', 
        'material_name', 
        'file_path', 
        'week'
    ];

    /**
     * Relasi ke Model ClassModel
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }

    /**
     * Relasi ke Pengajar (User)
     */
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }
}