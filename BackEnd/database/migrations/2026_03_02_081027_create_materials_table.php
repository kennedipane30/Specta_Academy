<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id('materialsID');
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // DATA MATERI SESUAI SEEDER
        $data = [
            1 => ['TIU', 'Psikotes', 'Bahasa Inggris', 'Matematika', 'TWK'],
            2 => ['TIU', 'Psikotes', 'Matematika', 'TWK'],
            3 => ['Matematika', 'Bahasa Inggris', 'Fisika', 'Biologi', 'Kimia'],
            4 => ['Matematika', 'Bahasa Inggris', 'Kimia', 'Biologi', 'Fisika', 'Psikotes'],
        ];

        foreach ($data as $classId => $subjects) {
            foreach ($subjects as $s) {
                DB::table('materials')->insert([
                    'class_id' => $classId,
                    'title' => 'Materi ' . $s,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
