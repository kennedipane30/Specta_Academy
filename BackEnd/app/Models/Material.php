<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model {
    protected $table = 'materials'; // Pastikan nama tabel benar
    protected $primaryKey = 'materialsID';

    // Tambahkan 'minggu' dan 'file_path' di sini
    protected $fillable = ['class_id', 'title', 'nama_materi', 'file_path', 'minggu'];
}
