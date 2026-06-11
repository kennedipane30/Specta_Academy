<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists('teacher_assignments');

        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();

            // 1. Relasi ke User (Guru)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');

            // 2. Relasi ke Kelas
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');

            // 3. Mata Pelajaran
            $table->unsignedBigInteger('subject_id')->nullable();

            // ✅ TAMBAHKAN KOLOM subject_name untuk menyimpan nama mata pelajaran
            $table->string('subject_name', 255)->nullable()->after('subject_id');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('teacher_assignments');
    }
};
