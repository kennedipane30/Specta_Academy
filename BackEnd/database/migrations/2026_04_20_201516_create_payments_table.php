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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->foreignId('user_id')->constrained('users', 'usersID')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes', 'class_id')->onDelete('cascade');
            $table->string('order_id')->unique(); // ID unik untuk Midtrans (Contoh: SPEKTA-12345)
            $table->integer('amount');
            $table->string('status')->default('pending'); // pending, success, failure, settlement
            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable(); // gopay, bank_transfer, dll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
