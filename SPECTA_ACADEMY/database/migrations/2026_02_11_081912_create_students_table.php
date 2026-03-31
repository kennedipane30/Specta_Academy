<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void {
    Schema::create('students', function (Blueprint $table) {
        $table->id('studentsID');
        $table->foreignId('user_id')->constrained('users', 'usersID')->onDelete('cascade');
        $table->string('parent_name')->nullable(); // Nama Orang Tua
        $table->string('school')->nullable();      // Gunakan ini untuk Alamat
        $table->string('wa_ortu')->nullable();
        $table->string('nisn')->nullable();       // NISN
        $table->date('dob')->nullable();          // Tanggal Lahir
        $table->string('grade')->default('12 IPA');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
