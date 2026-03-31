<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. BUAT TABEL ROLES DULU
        Schema::create('roles', function (Blueprint $table) {
            $table->id('rolesID'); // Sesuai ERD
            $table->string('name');
            $table->timestamps();
        });

        // 2. BUAT TABEL USERS
// database/migrations/xxxx_create_users_table.php
    Schema::create('users', function (Blueprint $table) {
        $table->id('usersID');
        $table->string('name')->unique(); // Nama unik untuk login
        $table->string('email')->unique();
        $table->string('phone');
        $table->string('password');
        $table->boolean('is_verified')->default(false); // Status Verifikasi
        $table->foreignId('role_id')->constrained('roles', 'rolesID');
        $table->timestamps();
    });

        // 3. TABEL PASSWORD RESET
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 4. TABEL SESSIONS (PENTING AGAR TIDAK ERROR 500 LAGI)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};
