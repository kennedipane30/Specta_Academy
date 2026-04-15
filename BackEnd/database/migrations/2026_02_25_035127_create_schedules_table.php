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
        Schema::create('schedules', function (Blueprint $table) {
            // MODIFIKASI: Primary Key bahasa Inggris
            $table->id('schedule_id');

            // MODIFIKASI: Merujuk ke tabel 'classes' dan kolom 'class_id'
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            /**
             * MODIFIKASI:
             * Merujuk ke tabel 'users' dan kolom 'usersID'
             * (Sesuai permintaan Anda untuk tidak mengubah migrasi user)
             */
            $table->foreignId('teacher_id')
                  ->constrained('users', 'usersID')
                  ->onDelete('cascade');

            $table->string('title'); // Contoh: Psychology / English
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
