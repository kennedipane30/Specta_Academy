<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id('promotion_id');
            // Merujuk ke tabel classes dan class_id
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');

            // 1. PERUBAHAN: Ditambah ->nullable() agar boleh kosong
            $table->string('image_banner')->nullable();

            $table->string('code')->unique();

            // 2. TAMBAHAN DARI FILE KEDUA (diletakkan setelah code)
            $table->string('discount_type')->default('percent');

            $table->integer('discount_percent');

            // 3. TAMBAHAN DARI FILE KEDUA (diletakkan setelah discount_percent)
            $table->integer('quota')->default(0);

            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
