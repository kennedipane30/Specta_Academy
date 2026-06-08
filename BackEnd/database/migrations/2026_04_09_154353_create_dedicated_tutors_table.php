<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dedicated_tutors', function (Blueprint $table) {
            $table->id('dedicated_tutor_id');

            // Merujuk ke student_id pada tabel students
            $table->foreignId('student_id')->constrained('students', 'student_id');

            // PERUBAHAN: foreignId diganti menjadi unsignedBigInteger biasa.
            // Ini akan membuat kolom untuk menyimpan ID, tapi TANPA foreign key (tidak di-constrain).
            $table->unsignedBigInteger('material_id');

            // Merujuk ke usersID pada tabel users
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

    public function down(): void
    {
        Schema::dropIfExists('dedicated_tutors');
    }
};

