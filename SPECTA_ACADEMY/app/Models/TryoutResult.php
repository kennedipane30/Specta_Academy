<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TryoutResult extends Model {
    // WAJIB ADA AGAR INSERT BERHASIL
    protected $primaryKey = 'resultsID';
    protected $fillable = ['user_id', 'tryout_id', 'score', 'total_correct'];
}
