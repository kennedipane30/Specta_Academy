<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'usersID'; // Pastikan PK ini ada

    protected $fillable = [
        'name', 'email', 'phone', 'role_id', 'password',
    ];

    // --- TAMBAHKAN RELASI INI (PENTING!) ---
    public function student()
    {
        // Menghubungkan user_id di tabel students dengan usersID di tabel users
        return $this->hasOne(Student::class, 'user_id', 'usersID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'rolesID');
    }
}
