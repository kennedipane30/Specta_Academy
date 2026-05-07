<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('tryout_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');   // Pengajar (usersID)
            $table->unsignedBigInteger('class_id');
            $table->string('subject_name');         // TIU/TWK/dll

            // Kolom Pertanyaan (Teks & Gambar)
            $table->text('question')->nullable();
            $table->string('question_image')->nullable();

            // Kolom Opsi A (Teks & Gambar)
            $table->string('option_a')->nullable();
            $table->string('option_a_image')->nullable();

            // Kolom Opsi B (Teks & Gambar)
            $table->string('option_b')->nullable();
            $table->string('option_b_image')->nullable();

            // Kolom Opsi C (Teks & Gambar)
            $table->string('option_c')->nullable();
            $table->string('option_c_image')->nullable();

            // Kolom Opsi D (Teks & Gambar)
            $table->string('option_d')->nullable();
            $table->string('option_d_image')->nullable();

            $table->string('correct_answer');       // Kunci (A/B/C/D)
            $table->text('explanation')->nullable();            // Pembahasan
            $table->timestamps();

            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('tryout_submissions');
    }
};
