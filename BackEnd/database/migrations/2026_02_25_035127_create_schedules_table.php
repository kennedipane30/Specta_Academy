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
        $table->id('schedule_id');

        // Relasi ke Kelas
        $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

        // Relasi ke Pengajar (Sesuaikan dengan usersID)
        $table->unsignedBigInteger('teacher_id');
        $table->foreign('teacher_id')->references('usersID')->on('users')->onDelete('cascade');

        $table->string('title'); // Contoh: "Materi Bahasa Inggris"
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
