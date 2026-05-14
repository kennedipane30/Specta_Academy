<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';
    protected $primaryKey = 'question_id';

    protected $fillable = [
        'class_id',       // ✨ Wajib ada agar bisa disimpan Admin
        'tryout_id',
        'question',
        'question_image', // ✨ Tambahkan semua kolom gambar ke fillable
        'option_a',
        'option_a_image',
        'option_b',
        'option_b_image',
        'option_c',
        'option_c_image',
        'option_d',
        'option_d_image',
        'correct_answer',
        'explanation'
    ];

    /**
     * ✨ RELASI KE KELAS
     * Agar Admin bisa memanggil $live->classModel->program_name di View
     */
    public function classModel()
    {
        return $this->belongsTo(ClassModel::class, 'class_id', 'class_id');
    }
}
