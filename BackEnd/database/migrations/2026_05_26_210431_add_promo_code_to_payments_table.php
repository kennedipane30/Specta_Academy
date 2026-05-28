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
            Schema::table('payments', function (Blueprint $table) {
                // Menambah kolom promo_code setelah class_id
                $table->string('promo_code')->nullable()->after('class_id');
            });
        }

        public function down(): void
        {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('promo_code');
            });
        }
};
