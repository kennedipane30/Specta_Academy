<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dedicated_tutors', function (Blueprint $table) {
            // 1. Hapus foreign key constraint
            $table->dropForeign(['material_id']); 
            
            // 2. Pastikan kolom tetap ada tapi tanpa 'constrained'
            // Kita biarkan kolomnya tetap bigInteger agar bisa menyimpan ID
        });
    }

    public function down(): void
    {
        Schema::table('dedicated_tutors', function (Blueprint $table) {
            // Jika ingin mengembalikan (opsional)
            // $table->foreign('material_id')->references('material_id')->on('materials');
        });
    }
};