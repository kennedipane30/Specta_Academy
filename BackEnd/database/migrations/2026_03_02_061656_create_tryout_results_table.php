<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tryout_results', function (Blueprint $table) {
            $table->id('tryout_result_id');
            $table->foreignId('user_id')->constrained('users', 'usersID'); // Referensi usersID sesuai file asli Anda
            $table->foreignId('tryout_id')->constrained('tryouts', 'tryout_id');
            $table->integer('score');
            $table->integer('total_correct');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryout_results');
    }
};
