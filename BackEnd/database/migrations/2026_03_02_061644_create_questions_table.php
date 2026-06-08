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

            // 1. Relasi
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');
            $table->foreignId('tryout_id')->nullable()->constrained('tryouts', 'tryout_id')->onDelete('cascade');

            $table->string('subject', 100)->nullable();
            $table->integer('question_number')->default(0);

            // 3. Konten Soal (HANYA TEKS)
            $table->text('question');

            // 4. Opsi Jawaban (HANYA TEKS)
            $table->text('option_a');
            $table->text('option_b');
            $table->text('option_c');
            $table->text('option_d');
            $table->text('option_e')->nullable();

            // 5. Kunci & Penjelasan
            $table->char('correct_answer', 1);
            $table->text('explanation')->nullable();

            // 6. Scoring
            $table->integer('points')->default(1);

            $table->timestamps();

            // INDEXES
            $table->index('tryout_id');
            $table->index('subject');
            $table->index('question_number');
            $table->index(['tryout_id', 'question_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
