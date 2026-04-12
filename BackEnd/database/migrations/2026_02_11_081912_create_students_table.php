<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            // Menggunakan Primary Key kustom
            $table->id('studentsID');

            // Foreign Key ke tabel users (Pastikan usersID adalah nama kolom PK di tabel users)
            $table->foreignId('user_id')
                  ->constrained('users', 'usersID')
                  ->onDelete('cascade');

            // Foreign Key ke tabel class_models (Wajib ada kolom class_id agar materi bisa muncul)
            $table->foreignId('class_id')
                  ->nullable()
                  ->constrained('class_models', 'class_modelsID')
                  ->onDelete('set null');

            $table->string('parent_name')->nullable();
            $table->string('school')->nullable();
            $table->string('wa_ortu')->nullable();
            $table->string('nisn')->nullable();
            $table->date('dob')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Untuk PostgreSQL, tambahkan perintah drop manual jika perlu
        Schema::dropIfExists('students');
    }
};
