<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_questions', function (Blueprint $table) {
            $table->id('practice_question_id');
            // MODIFIKASI: Merujuk ke tabel 'classes' dan primary key 'class_id'
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            $table->string('subject');
            $table->integer('week');
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_questions');
    }
};
