<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id('promotionsID');
            // TAMBAHKAN RELASI KE KELAS (Target Promo)
            $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');

            $table->string('image_banner');
            $table->string('code')->unique();
            $table->integer('discount_percent');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
