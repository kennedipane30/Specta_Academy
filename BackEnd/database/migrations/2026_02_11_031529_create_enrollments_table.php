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
        Schema::create('enrollments', function (Blueprint $table) {
            // Primary Key untuk tabel ini sendiri (Bahasa Inggris)
            $table->id('enrollment_id');

            /**
             * Hubungan ke tabel users:
             * Kita tetap menamakan kolom di sini 'user_id',
             * TAPI kita arahkan referensinya ke 'usersID' (sesuai file asli users Anda)
             */
            $table->foreignId('user_id')
                  ->constrained('users', 'usersID') // 'usersID' adalah PK asli di tabel users
                  ->onDelete('cascade');

            // Hubungan ke tabel classes (Merujuk ke 'class_id' yang sudah kita ubah sebelumnya)
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            $table->string('payment_proof');

            // Status dalam bahasa Inggris
            $table->enum('status', ['pending', 'active', 'expired'])->default('pending');

            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
