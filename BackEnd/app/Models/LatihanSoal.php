<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LatihanSoal extends Model {
    protected $table = 'latihan_soals';
    protected $primaryKey = 'latihan_soalID';
    protected $fillable = ['class_id', 'subject', 'minggu', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'jawaban_benar', 'pembahasan'];
}
