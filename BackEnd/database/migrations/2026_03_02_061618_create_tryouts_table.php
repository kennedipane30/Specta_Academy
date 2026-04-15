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
            // MODIFIKASI: Primary Key bahasa Inggris
            $table->id('tryout_id');

            /**
             * MODIFIKASI:
             * Merujuk ke tabel 'classes' dan kolom 'class_id'
             * (Ini memperbaiki error "relation class_models does not exist")
             */
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            $table->string('title');
            $table->integer('duration'); // Durasi dalam menit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};
