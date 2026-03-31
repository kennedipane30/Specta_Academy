<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void {
    Schema::create('schedules', function (Blueprint $table) {
        $table->id('schedulesID');
        // Relasi ke Kelas (ID 1-4)
        $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
        // Relasi ke Pengajar (UserID dengan role 2)
        $table->foreignId('teacher_id')->constrained('users', 'usersID')->onDelete('cascade');

        $table->string('title'); // Contoh: Psikologi / Bahasa Inggris
        $table->date('date');    // Tanggal pertemuan
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
