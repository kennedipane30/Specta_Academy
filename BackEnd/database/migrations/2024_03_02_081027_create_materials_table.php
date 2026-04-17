<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            // MODIFIKASI: Primary Key bahasa Inggris
            $table->id('material_id');

            // MODIFIKASI: Merujuk ke tabel 'classes' dan 'class_id' yang baru
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            $table->string('title');

            // MODIFIKASI: Menambahkan kolom yang sebelumnya ada di migrasi terpisah
            $table->string('material_name')->nullable(); // Sebelumnya: nama_materi
            $table->integer('week')->nullable();         // Sebelumnya: minggu

            $table->string('file_path')->nullable();
            $table->timestamps();
        });

        // DATA MATERI SESUAI SEEDER (Diterjemahkan ke Bahasa Inggris)
        $data = [
            1 => ['TIU', 'Psychological Test', 'English', 'Mathematics', 'TWK'],
            2 => ['TIU', 'Psychological Test', 'Mathematics', 'TWK'],
            3 => ['Mathematics', 'English', 'Physics', 'Biology', 'Chemistry'],
            4 => ['Mathematics', 'English', 'Chemistry', 'Biology', 'Physics', 'Psychological Test'],
        ];

        foreach ($data as $classId => $subjects) {
            foreach ($subjects as $s) {
                DB::table('materials')->insert([
                    'class_id'      => $classId,
                    'title'         => $s . ' Material', // Contoh: TIU Material
                    'material_name' => $s,
                    'week'          => 1, // Default week 1
                    'created_at'    => now(),
                    'updated_at'    => now()
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
