<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_models', function (Blueprint $table) {
            $table->id('class_modelsID');
            $table->string('nama_program');
            $table->string('gambar');
            $table->integer('price')->default(900000);
            $table->timestamps();
        });

        // ISI DATA 4 PROGRAM UTAMA
        DB::table('class_models')->insert([
            ['class_modelsID' => 1, 'nama_program' => 'CALON ABDI NEGARA', 'gambar' => 'abdi_negara.png', 'created_at' => now()],
            ['class_modelsID' => 2, 'nama_program' => 'PTN & UNHAN', 'gambar' => 'ptn.png', 'created_at' => now()],
            ['class_modelsID' => 3, 'nama_program' => 'SMA & SMP REGULER', 'gambar' => 'reguler.png', 'created_at' => now()],
            ['class_modelsID' => 4, 'nama_program' => 'SMA FAVORIT', 'gambar' => 'favorit.png', 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('class_models');
    }
};
