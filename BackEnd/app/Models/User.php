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

    // protected $fillable = [
    //     'name', 'email', 'phone', 'role_id', 'password',
    // ];

    protected $fillable = [
    'name', 'email', 'phone', 'role_id', 'password', 'is_verified', // Tambahkan 'is_verified' di sini
];

    public function student()
    {
        // Parameter: Model, Foreign Key di tabel students, Local Key di tabel users
        return $this->hasOne(Student::class, 'user_id', 'usersID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'rolesID');
    }
    public function classes()
{
    return $this->belongsToMany(ClassModel::class, 'enrollments', 'user_id', 'class_id')
                ->withPivot('status', 'payment_proof')

             ->withTimestamps();
}

public function assignments()
{
    return $this->hasMany(TeacherAssignment::class, 'user_id', 'usersID');
}

}
