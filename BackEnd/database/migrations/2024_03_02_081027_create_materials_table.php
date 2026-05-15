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
            // Primary Key
            $table->id('material_id');

            // Foreign Key ke Tabel Classes
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            // ✨ TAMBAHKAN KOLOM INI: Untuk mencatat pengajar mana yang upload
            // Gunakan nullable() agar data seeder di bawah tidak error
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('title');
            $table->string('material_name')->nullable();
            $table->integer('week')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();

            // ✨ TAMBAHKAN RELASI KE TABEL USERS (PK: usersID)
            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('set null');
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
                    'user_id'       => null, // Default null untuk data awal seeder
                    'title'         => $s . ' Material',
                    'material_name' => $s,
                    'week'          => 1,
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
