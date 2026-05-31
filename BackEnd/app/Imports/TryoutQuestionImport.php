<?php

namespace App\Imports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TryoutQuestionImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $tryoutId;
    protected $subject;
    protected $startNumber;
    
    public function __construct($tryoutId, $subject, $startNumber = 1)
    {
        $this->tryoutId = $tryoutId;
        $this->subject = $subject;
        $this->startNumber = $startNumber;
    }
    
    public function model(array $row)
    {
        return new Question([
            'tryout_id' => $this->tryoutId,
            'subject' => $this->subject,
            'question_number' => $this->startNumber + ($row['no'] - 1),
            'question' => $row['pertanyaan'],
            'question_image' => $row['gambar_pertanyaan'] ?? null,
            'option_a' => $row['opsi_a'],
            'option_b' => $row['opsi_b'],
            'option_c' => $row['opsi_c'],
            'option_d' => $row['opsi_d'],
            'option_a_image' => $row['gambar_opsi_a'] ?? null,
            'option_b_image' => $row['gambar_opsi_b'] ?? null,
            'option_c_image' => $row['gambar_opsi_c'] ?? null,
            'option_d_image' => $row['gambar_opsi_d'] ?? null,
            'correct_answer' => $row['kunci_jawaban'],
            'explanation' => $row['pembahasan'],
            'points' => $row['poin'] ?? 1,
        ]);
    }
    
    public function rules(): array
    {
        return [
            'no' => 'required|integer',
            'pertanyaan' => 'required|string',
            'opsi_a' => 'required|string',
            'opsi_b' => 'required|string',
            'opsi_c' => 'required|string',
            'opsi_d' => 'required|string',
            'kunci_jawaban' => 'required|in:A,B,C,D',
        ];
    }
}