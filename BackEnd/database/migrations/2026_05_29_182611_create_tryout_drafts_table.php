<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('tryout_drafts', function (Blueprint $table) {
            $table->id();
            $table->integer('class_id');

            // PERUBAHAN: Kolom user_id dimasukkan langsung ke sini (diletakkan setelah class_id)
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('subject_name');
            $table->text('question');
            $table->string('question_image')->nullable();
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('option_e');
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_drafts');
    }
};
