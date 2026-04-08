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
    Schema::create('latihan_soals', function (Blueprint $table) {
        $table->id('latihan_soalID');
        $table->foreignId('class_id')->constrained('class_models', 'class_modelsID')->onDelete('cascade');
        $table->string('subject');       // Contoh: Matematika
        $table->integer('minggu');      // 1-20
        $table->text('pertanyaan');
        $table->string('opsi_a');
        $table->string('opsi_b');
        $table->string('opsi_c');
        $table->string('opsi_d');
        $table->string('jawaban_benar'); // A/B/C/D
        $table->text('pembahasan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('latihan_soals');
    }
};
