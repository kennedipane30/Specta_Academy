<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model {
    protected $primaryKey = 'otp_codesID'; // PK sesuai ERD
    protected $fillable = ['user_id', 'otp', 'valid_until'];
}
