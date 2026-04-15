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
        Schema::create('attendances', function (Blueprint $table) {
            // MODIFIKASI: Primary Key bahasa Inggris
            $table->id('attendance_id');

            /**
             * MODIFIKASI:
             * Merujuk ke tabel 'schedules' dan kolom 'schedule_id'
             * (Ini memperbaiki error pada screenshot Anda)
             */
            $table->foreignId('schedule_id')
                  ->constrained('schedules', 'schedule_id')
                  ->onDelete('cascade');

            /**
             * MODIFIKASI:
             * Merujuk ke tabel 'users' dan kolom 'usersID'
             */
            $table->foreignId('user_id')
                  ->constrained('users', 'usersID')
                  ->onDelete('cascade');

            // MODIFIKASI: Status dalam bahasa Inggris
            $table->enum('status', ['present', 'permission', 'absent']);

            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
