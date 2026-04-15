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

            $table->string('image_banner');
            $table->string('code')->unique();
            $table->integer('discount_percent');
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
