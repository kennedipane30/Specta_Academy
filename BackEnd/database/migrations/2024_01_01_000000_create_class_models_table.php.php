<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MODIFIKASI: Nama tabel menjadi 'classes' agar dikenali oleh tabel lain
        Schema::create('classes', function (Blueprint $table) {
            // MODIFIKASI: PK menjadi 'class_id'
            $table->id('class_id');
            $table->string('program_name'); // Sebelumnya: nama_program
            $table->string('image');        // Sebelumnya: gambar
            $table->integer('price')->default(900000);
            $table->timestamps();
        });

        // ISI DATA 4 PROGRAM UTAMA (Sesuaikan kolom dengan yang baru di atas)
        DB::table('classes')->insert([
            [
                'class_id' => 1,
                'program_name' => 'CALON ABDI NEGARA',
                'image' => 'abdi_negara.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'class_id' => 2,
                'program_name' => 'PTN & UNHAN',
                'image' => 'ptn.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'class_id' => 3,
                'program_name' => 'SMA & SMP REGULER',
                'image' => 'reguler.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'class_id' => 4,
                'program_name' => 'SMA FAVORIT',
                'image' => 'favorit.png',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    public function down(): void
    {
        // Ubah sesuai nama tabel baru
        Schema::dropIfExists('classes');
    }
};
