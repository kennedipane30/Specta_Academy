<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan pengecekan agar tidak terjadi error duplicate column
        if (!Schema::hasColumn('schedules', 'meeting_link')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->string('meeting_link')->nullable()->after('end_time');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('schedules', 'meeting_link')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropColumn('meeting_link');
            });
        }
    }
};
