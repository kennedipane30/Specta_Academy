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
            $table->id('enrollment_id');

            $table->foreignId('user_id')
                  ->constrained('users', 'usersID')
                  ->onDelete('cascade');

            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            $table->string('payment_proof')->nullable(); // ✅ UBAH: tambah ->nullable()

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
