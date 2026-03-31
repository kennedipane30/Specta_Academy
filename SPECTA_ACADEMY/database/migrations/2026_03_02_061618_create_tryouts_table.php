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
        Schema::create('tryouts', function (Blueprint $table) {
            $table->id('tryoutsID'); // PK Sesuai ERD
            // Pastikan merujuk ke class_modelsID pada tabel class_models
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
            $table->string('title');
            $table->integer('duration');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};

