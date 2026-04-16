<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model {
    // MODIFIKASI: Sesuaikan dengan pgAdmin
    protected $primaryKey = 'tryout_result_id';

    protected $fillable = ['user_id', 'tryout_id', 'score', 'total_correct'];

    // Relasi ke User (Siswa)
    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'usersID');
    }

    // Relasi ke Tryout agar nama ujian (Batch 1, dll) muncul
    public function tryout() {
        return $this->belongsTo(Tryout::class, 'tryout_id', 'tryout_id');
    }
}
