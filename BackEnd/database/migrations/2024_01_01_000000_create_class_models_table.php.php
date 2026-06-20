<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CEK apakah tabel sudah ada sebelum membuat
        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->id('class_id');
                $table->string('program_name');
                $table->string('image')->nullable();
                $table->integer('price')->default(900000);
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->timestamps();
            });
        } else {
            // Jika tabel sudah ada, tambahkan kolom yang belum ada
            Schema::table('classes', function (Blueprint $table) {
                if (!Schema::hasColumn('classes', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('classes', 'image_url')) {
                    $table->string('image_url')->nullable();
                }
                if (!Schema::hasColumn('classes', 'price')) {
                    $table->integer('price')->default(0);
                }
            });
        }

        // ISI DATA 4 PROGRAM UTAMA (hanya jika tabel kosong)
        if (DB::table('classes')->count() === 0) {
            DB::table('classes')->insert([
                [
                    'class_id' => 1,
                    'program_name' => 'CALON ABDI NEGARA',
                    'image' => 'abdi_negara.png',
                    'price' => 900000,
                    'description' => 'Program persiapan Calon Abdi Negara',
                    'image_url' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'class_id' => 2,
                    'program_name' => 'PTN & UNHAN',
                    'image' => 'ptn.png',
                    'price' => 900000,
                    'description' => 'Program persiapan PTN & UNHAN',
                    'image_url' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'class_id' => 3,
                    'program_name' => 'SMA & SMP REGULER',
                    'image' => 'reguler.png',
                    'price' => 900000,
                    'description' => 'Program reguler SMA & SMP',
                    'image_url' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'class_id' => 4,
                    'program_name' => 'SMA FAVORIT',
                    'image' => 'favorit.png',
                    'price' => 900000,
                    'description' => 'Program SMA Favorit',
                    'image_url' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
