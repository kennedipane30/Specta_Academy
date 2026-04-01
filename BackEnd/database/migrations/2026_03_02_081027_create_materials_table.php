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
        Schema::create('materials', function (Blueprint $table) {
            $table->id('materialsID');
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->timestamps();

            // MODIFIKASI SAKTI: Gabungan class_id dan title harus UNIK
            $table->unique(['class_id', 'title']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
