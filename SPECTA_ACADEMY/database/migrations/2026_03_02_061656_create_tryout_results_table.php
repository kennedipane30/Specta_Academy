<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tryout_results', function (Blueprint $table) {
            $table->id('resultsID');
            $table->foreignId('user_id')->constrained('users', 'usersID');
            $table->foreignId('tryout_id')->constrained('tryouts', 'tryoutsID');
            $table->integer('score');
            $table->integer('total_correct');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryout_results');
    }
};
