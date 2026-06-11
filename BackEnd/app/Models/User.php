<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'usersID';

    protected $fillable = [
        'name', 'email', 'phone', 'role_id', 'password', 'is_verified', 'photo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['photo_url'];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id', 'usersID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'rolesID');
    }

    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'enrollments', 'user_id', 'class_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Relasi ke TeacherAssignment (penugasan mengajar)
     * ✅ SUDAH BENAR - tidak perlu diubah
     */
    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'user_id', 'usersID');
    }
}
