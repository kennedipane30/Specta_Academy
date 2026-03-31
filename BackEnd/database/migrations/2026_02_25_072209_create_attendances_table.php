<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void {
    Schema::create('attendances', function (Blueprint $table) {
        $table->id('attendancesID');
        $table->foreignId('schedule_id')->constrained('schedules', 'schedulesID')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users', 'usersID'); // ID Siswa
        $table->enum('status', ['hadir', 'izin', 'alpa']);
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
