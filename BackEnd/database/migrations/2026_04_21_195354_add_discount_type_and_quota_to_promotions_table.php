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
    Schema::table('promotions', function (Blueprint $table) {
        // Tambahkan kolom yang kurang
        $table->string('discount_type')->default('percent')->after('code');
        $table->integer('quota')->default(0)->after('discount_percent');
        
        // Buat image_banner jadi boleh kosong (nullable) agar tidak error lagi
        $table->string('image_banner')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            //
        });
    }
};
