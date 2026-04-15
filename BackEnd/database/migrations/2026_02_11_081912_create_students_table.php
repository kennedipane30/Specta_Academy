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
        Schema::create('students', function (Blueprint $table) {
            // Primary Key tabel students
            $table->id('student_id');

            /**
             * MODIFIKASI:
             * Nama kolom di tabel ini tetap 'user_id' (Bahasa Inggris)
             * TAPI referensinya diarahkan ke 'usersID' (sesuai file asli users Anda)
             */
            $table->foreignId('user_id')
                  ->constrained('users', 'usersID')
                  ->onDelete('cascade');

            // Foreign Key ke tabel classes (Merujuk ke 'class_id' yang sudah kita buat)
            $table->foreignId('class_id')
                  ->nullable()
                  ->constrained('classes', 'class_id')
                  ->onDelete('set null');

            $table->string('parent_name')->nullable();
            $table->text('address')->nullable();             // school diganti address
            $table->string('parent_phone')->nullable();       // wa_ortu diganti parent_phone
            $table->string('national_id_number')->nullable(); // nisn diganti national_id_number
            $table->date('date_of_birth')->nullable();       // dob diganti date_of_birth
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
