<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id('question_bank_id');
            // Menghubungkan ke usersID di tabel users (Sesuai database Anda)
            $table->foreignId('user_id')->constrained('users', 'usersID')->onDelete('cascade');
            $table->string('title');
            $table->string('subject');
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('question_banks');
    }
};
