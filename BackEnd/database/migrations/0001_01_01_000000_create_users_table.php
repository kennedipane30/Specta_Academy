<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini
use Illuminate\Support\Facades\Hash; // Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        // 1. BUAT TABEL ROLES
        Schema::create('roles', function (Blueprint $table) {
            $table->id('rolesID');
            $table->string('name');
            $table->timestamps();
        });

        // ISI DATA ROLES
        DB::table('roles')->insert([
            ['rolesID' => 1, 'name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['rolesID' => 2, 'name' => 'pengajar', 'created_at' => now(), 'updated_at' => now()],
            ['rolesID' => 3, 'name' => 'siswa', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. BUAT TABEL USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id('usersID');
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('password');
            $table->boolean('is_verified')->default(false);
            $table->foreignId('role_id')->constrained('roles', 'rolesID');
            $table->timestamps();
        });

        // ISI DATA USER DEFAULT
        DB::table('users')->insert([
            [
                'name' => 'Admin Spekta',
                'email' => 'admin@gmail.com',
                'phone' => '08123456789',
                'password' => Hash::make('password123'),
                'is_verified' => true,
                'role_id' => 1,
                'created_at' => now(), 'updated_at' => now()
            ],
            [
                'name' => 'Pak Guru Spekta',
                'email' => 'guru@gmail.com',
                'phone' => '08123456788',
                'password' => Hash::make('password123'),
                'is_verified' => true,
                'role_id' => 2,
                'created_at' => now(), 'updated_at' => now()
            ]
        ]);

        // 3. TABEL PASSWORD RESET
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 4. TABEL SESSIONS
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
