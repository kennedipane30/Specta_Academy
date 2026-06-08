<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('tryout_submissions', function (Blueprint $table) {
            $table->id();

            // Relasi ke User
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');

            // Relasi ke Paket Tryout
            $table->foreignId('tryout_id')->constrained('tryouts', 'tryout_id')->onDelete('cascade');

            // 🔥 Menyimpan jawaban siswa dalam format JSON (Contoh: {"12":"A", "13":"C"})
            $table->text('answers');

            // Skor Akhir
            $table->float('score');

            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tryout_submissions');
    }
};
