<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Hapus tabel jika sudah ada untuk menghindari error saat migrasi ulang
        Schema::dropIfExists('teacher_assignments');

        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();

            // 1. Relasi ke User (Guru)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');

            // 2. Relasi ke Kelas
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');

            // 3. Relasi ke Mata Pelajaran
            // MODIFIKASI: Diarahkan ke tabel 'materials' kolom 'material_id'
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')
                  ->references('material_id')
                  ->on('materials')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('teacher_assignments');
    }
};
