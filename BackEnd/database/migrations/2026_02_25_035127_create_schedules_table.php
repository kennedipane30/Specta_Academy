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
            // Primary Key: schedule_id
            $table->id('schedule_id');

            // 1. Relasi ke Kelas
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

            // 2. Relasi ke Pengajar (User) - merujuk ke 'usersID' di tabel 'users'
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')->references('usersID')->on('users')->onDelete('cascade');

            // 3. Relasi ke Mata Pelajaran (Subject)
            // Diubah dari subject_name (string) menjadi subject_id (Foreign Key)
            // agar bisa menggunakan relasi $this->belongsTo(Subject::class) di Model
            $table->foreignId('subject_id')->constrained('subjects', 'subject_id')->onDelete('cascade');

            // 4. Detail Pembelajaran
            $table->string('title'); // Judul Materi

            // 5. Link Meeting
            $table->string('meeting_link')->nullable(); 

            // 6. Waktu Pelaksanaan
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            // 7. Status Jadwal
            $table->enum('status', ['scheduled', 'ongoing', 'finished', 'canceled'])->default('scheduled');

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