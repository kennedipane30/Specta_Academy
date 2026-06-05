<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan pengecekan agar tidak error jika tabel sudah ada
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id('material_id');

                $table->foreignId('class_id')
                      ->constrained('classes', 'class_id')
                      ->onDelete('cascade');

                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title');
                $table->string('material_name')->nullable();
                $table->integer('week')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('usersID')->on('users')->onDelete('set null');
            });
        }

        // Seeding data - Pastikan ID class 1, 2, 3, 4 sudah ada di tabel classes!
        $data = [
            1 => ['TIU', 'Psychological Test', 'Mathematics', 'TWK', 'TKP'],
            2 => ['TIU', 'Psychological Test', 'Mathematics', 'TWK' , 'TKP' , 'English', 'Fisika' , 'Kimia'],
            3 => ['Mathematics', 'English', 'Fisika' ,'Biology', 'Chemistry' ,'Kimia'],
            4 => ['Mathematics', 'English', 'Chemistry', 'Biology', 'Fisika', 'Psychological Test'],
        ];

        foreach ($data as $classId => $subjects) {
            foreach ($subjects as $s) {
                // Cek apakah data sudah ada agar tidak duplikat saat migrasi dijalankan ulang
                $exists = DB::table('materials')
                            ->where('class_id', $classId)
                            ->where('material_name', $s)
                            ->exists();

                if (!$exists) {
                    DB::table('materials')->insert([
                        'class_id'      => $classId,
                        'user_id'       => null,
                        'title'         => $s . ' Material',
                        'material_name' => $s,
                        'week'          => 1,
                        'created_at'    => now(),
                        'updated_at'    => now()
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
