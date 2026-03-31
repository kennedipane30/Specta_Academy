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
        Schema::create('class_models', function (Blueprint $table) {
            $table->id('class_modelsID');
            $table->string('nama_program');
            $table->string('gambar');
            $table->integer('price')->default(900000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_models');
    }
};
