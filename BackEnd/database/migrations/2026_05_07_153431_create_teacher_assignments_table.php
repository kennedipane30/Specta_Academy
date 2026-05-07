<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Merujuk ke usersID
            $table->unsignedBigInteger('class_id'); // Merujuk ke class_id di tabel classes
            $table->string('subject_name');         // Contoh: 'TIU', 'TWK', 'English'
            $table->timestamps();

            $table->foreign('user_id')->references('usersID')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('teacher_assignments');
    }
};
