<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
    Schema::create('attendances', function (Blueprint $table) {
        $table->id('attendance_id');
        $table->unsignedBigInteger('user_id');    // ID Siswa (usersID)
        $table->unsignedBigInteger('teacher_id'); // ID Pengajar (usersID)
        $table->unsignedBigInteger('class_id');   // ID Kelas
        $table->string('subject_name');           // TIU, TWK, dll
        $table->integer('week');                  // Minggu 1 - 20
        $table->enum('status', ['h', 'i', 'a']);  // Hadir, Izin, Alpa
        $table->date('date');                     // Tanggal absen dilakukan
        $table->timestamps();

        $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');
        $table->foreign('teacher_id')->references('usersID')->on('users')->onDelete('cascade');
    });
}
    public function down(): void {
        Schema::dropIfExists('attendances');
    }
};
