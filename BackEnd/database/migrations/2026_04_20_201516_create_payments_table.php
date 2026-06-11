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
            $table->string('order_id')->unique();
            $table->integer('amount');
            $table->string('promo_code')->nullable(); // ✅ TAMBAH INI
            $table->string('status')->default('pending');
            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('paid_at')->nullable(); // ✅ TAMBAH INI
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
