<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id('materialsID'); // Primary Key sesuai ERD kamu

            // Relasi ke tabel class_models (Induknya)
            // Pastikan on('class_models') dan references('class_modelsID') sesuai database kamu
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');

            $table->string('title');
            $table->string('file_path')->nullable(); // Untuk link video atau path file
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
