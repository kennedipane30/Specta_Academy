<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tryout extends Model
{
    use SoftDeletes;
    
    protected $primaryKey = 'tryout_id';
    
    protected $table = 'tryouts';
    
    protected $fillable = [
        'class_id',
        'title',
        'description',
        'duration_minutes',
        'total_questions',
        'status',
        'start_date',
        'end_date',
        'is_scheduled',
        'is_free',
        'price',
        'max_attempts',
        'show_leaderboard',
        'show_explanation',
        'passing_grade',
        'thumbnail',
        'banner',
        'tags',
        'created_by'
    ];
    
    protected $casts = [
        'tags' => 'array',
        'is_scheduled' => 'boolean',
        'is_free' => 'boolean',
        'show_leaderboard' => 'boolean',
        'show_explanation' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    // Relationship
    public function questions()
    {
        return $this->hasMany(Question::class, 'tryout_id', 'tryout_id');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'usersID');
    }
    
    // ✨ MODIFIKASI: Nama fungsi diubah agar tidak bentrok dengan keyword PHP
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
    
    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }
    
    public function scopeUpcoming($query)
    {
        return $query->where('is_scheduled', true)
                     ->where('start_date', '>', now())
                     ->where('status', 'published');
    }
}