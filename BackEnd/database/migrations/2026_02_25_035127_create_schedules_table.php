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
            $table->unsignedBigInteger('class_id');
            $table->foreign('class_id')
                  ->references('class_id')
                  ->on('classes')
                  ->onDelete('cascade');

            // 2. Relasi ke Pengajar (User) - merujuk ke 'usersID' di tabel 'users'
            $table->unsignedBigInteger('teacher_id');
            $table->foreign('teacher_id')
                  ->references('usersID')
                  ->on('users')
                  ->onDelete('cascade');

            // 3. Relasi ke Mata Pelajaran (MODIFIKASI: Merujuk ke tabel materials)
            // Kita arahkan ke 'materials' karena tabel 'subjects' tidak Anda gunakan/tidak ada.
            $table->unsignedBigInteger('subject_id');
            $table->foreign('subject_id')
                  ->references('material_id') // Kolom target di tabel materials
                  ->on('materials')           // Nama tabel target
                  ->onDelete('cascade');

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
