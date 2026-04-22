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
    Schema::create('promotions', function (Blueprint $table) {
        $table->id('promotion_id');
        $table->unsignedBigInteger('class_id');
        $table->string('code')->unique();
        $table->string('discount_type'); // 'percent' atau 'fixed'
        $table->decimal('discount_percent', 15, 2); // Nilai diskon
        $table->integer('quota')->default(0); // Kuota pemakai
        $table->string('image_banner')->nullable(); // Kita buat nullable agar tidak error
        $table->date('start_date');
        $table->date('end_date');
        $table->integer('is_active')->default(1);
        $table->timestamps();

        $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
    });
}

public function down()
{
    Schema::dropIfExists('promotions');
}
};