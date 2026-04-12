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
        $table->id();

        // Pastikan 'student_id' merujuk ke 'studentsID'
        $table->foreignId('student_id')->constrained('students', 'studentsID');

        // Pastikan 'material_id' merujuk ke 'materialsID'
        $table->foreignId('material_id')->constrained('materials', 'materialsID');

        // --- PERBAIKAN DI SINI ---
        // Ganti 'userID' menjadi 'usersID' (tambah huruf s)
        $table->foreignId('teacher_id')
              ->nullable()
              ->constrained('users', 'usersID')
              ->onDelete('set null');

        $table->date('date');
        $table->time('time');
        $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
        $table->timestamps();
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
