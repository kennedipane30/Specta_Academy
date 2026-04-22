<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('classes', function (Blueprint $table) {
        // Cek apakah kolom 'price' belum ada
        if (!Schema::hasColumn('classes', 'price')) {
            $table->integer('price')->default(0);
        }

        // Cek apakah kolom 'description' belum ada
        if (!Schema::hasColumn('classes', 'description')) {
            $table->text('description')->nullable();
        }

        // Cek apakah kolom 'image_url' belum ada
        if (!Schema::hasColumn('classes', 'image_url')) {
            $table->string('image_url')->nullable();
        }
    });
}

public function down()
{
    Schema::table('classes', function (Blueprint $table) {
        // Hapus hanya jika kolom ada (opsional untuk keamanan)
        if (Schema::hasColumn('classes', 'price')) $table->dropColumn('price');
        if (Schema::hasColumn('classes', 'description')) $table->dropColumn('description');
        if (Schema::hasColumn('classes', 'image_url')) $table->dropColumn('image_url');
    });
}
};
