<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class ClassContentSeeder extends Seeder
{
    public function run(): void
    {
        // Data Materi Sesuai Catatan Tangan
        $data = [
            1 => ['TIU', 'Psikotes', 'Bahasa Inggris', 'Matematika', 'TWK'],
            2 => ['TIU', 'Psikotes', 'Matematika', 'TWK'],
            3 => ['Matematika', 'Bahasa Inggris', 'Fisika', 'Biologi', 'Kimia'],
            4 => ['Matematika', 'Bahasa Inggris', 'Kimia', 'Biologi', 'Fisika', 'Psikotes'],
        ];

        foreach ($data as $classId => $subjects) {
            foreach ($subjects as $s) {
                Material::create([
                    'class_id' => $classId,
                    'title'    => 'Materi ' . $s
                ]);
            }
        }
    }
}
