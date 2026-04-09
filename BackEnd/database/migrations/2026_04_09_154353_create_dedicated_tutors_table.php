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
Schema::create('dedicated_tutors', function (Blueprint $table) {
    $table->id('dedicated_tutorsID');
    $table->unsignedBigInteger('student_id'); // Relasi ke studentsID
    $table->unsignedBigInteger('teacher_id'); // Relasi ke usersID (role pengajar)
    $table->unsignedBigInteger('material_id'); // Relasi ke materialsID
    $table->date('date');
    $table->time('time');
    $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
    $table->timestamps();

    $table->foreign('student_id')->references('studentsID')->on('students')->onDelete('cascade');
    $table->foreign('teacher_id')->references('usersID')->on('users')->onDelete('cascade');
    $table->foreign('material_id')->references('materialsID')->on('materials')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dedicated_tutors');
    }
};
