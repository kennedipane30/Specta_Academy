<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id'); // Primary Key

            // ✨ TAMBAHKAN: Relasi ke Kelas (Penyebab Error Sebelumnya)
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

            // Tetap pertahankan tryout_id jika Anda ingin mengelompokkan per paket ujian
            $table->foreignId('tryout_id')->nullable()->constrained('tryouts', 'tryout_id')->onDelete('cascade');

            // Konten Soal (Teks & Gambar)
            $table->text('question');
            $table->string('question_image')->nullable(); // ✨ Tambahan untuk Gambar

            // Opsi A-D (Teks)
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');

            // Opsi A-D (Gambar) ✨ Tambahan untuk Sistem Hybrid
            $table->string('option_a_image')->nullable();
            $table->string('option_b_image')->nullable();
            $table->string('option_c_image')->nullable();
            $table->string('option_d_image')->nullable();

            $table->char('correct_answer', 1);
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
