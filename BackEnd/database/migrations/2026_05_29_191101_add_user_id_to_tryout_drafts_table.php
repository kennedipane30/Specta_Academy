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
    Schema::table('tryout_drafts', function (Blueprint $table) {
        // Tambahkan kolom user_id
        $table->unsignedBigInteger('user_id')->nullable()->after('class_id');
    });
}

public function down(): void
{
    Schema::table('tryout_drafts', function (Blueprint $table) {
        $table->dropColumn('user_id');
    });
}
};
