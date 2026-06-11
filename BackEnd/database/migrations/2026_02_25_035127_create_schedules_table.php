<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('schedule_id');

            // Relasi ke Kelas
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')
                  ->references('class_id')
                  ->on('classes')
                  ->onDelete('cascade');

            // Relasi ke Pengajar
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')
                  ->references('usersID')
                  ->on('users')
                  ->onDelete('cascade');

            // Kolom mata pelajaran (tanpa foreign key)
            $table->unsignedBigInteger('subject_id')->nullable();

            // Detail Jadwal
            $table->string('title');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            // Status Jadwal
            $table->enum('status', ['scheduled', 'ongoing', 'finished', 'canceled'])->default('scheduled');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
