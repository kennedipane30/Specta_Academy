<?php
// database/migrations/2026_05_28_153218_modify_tryouts_table_add_fields.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            // Cek dan tambah kolom jika belum ada
            if (!Schema::hasColumn('tryouts', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('tryouts', 'duration_minutes')) {
                // Ubah duration ke duration_minutes jika ada
                if (Schema::hasColumn('tryouts', 'duration')) {
                    $table->renameColumn('duration', 'duration_minutes');
                } else {
                    $table->integer('duration_minutes')->default(120)->after('description');
                }
            }
            
            if (!Schema::hasColumn('tryouts', 'total_questions')) {
                $table->integer('total_questions')->default(0)->after('duration_minutes');
            }
            
            if (!Schema::hasColumn('tryouts', 'status')) {
                $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'archived'])
                      ->default('draft')->after('total_questions');
            }
            
            if (!Schema::hasColumn('tryouts', 'start_date')) {
                $table->dateTime('start_date')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('tryouts', 'end_date')) {
                $table->dateTime('end_date')->nullable()->after('start_date');
            }
            
            if (!Schema::hasColumn('tryouts', 'is_scheduled')) {
                $table->boolean('is_scheduled')->default(false)->after('end_date');
            }
            
            if (!Schema::hasColumn('tryouts', 'is_free')) {
                $table->boolean('is_free')->default(true)->after('is_scheduled');
            }
            
            if (!Schema::hasColumn('tryouts', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('is_free');
            }
            
            if (!Schema::hasColumn('tryouts', 'max_attempts')) {
                $table->integer('max_attempts')->default(1)->after('price');
            }
            
            if (!Schema::hasColumn('tryouts', 'show_leaderboard')) {
                $table->boolean('show_leaderboard')->default(true)->after('max_attempts');
            }
            
            if (!Schema::hasColumn('tryouts', 'show_explanation')) {
                $table->boolean('show_explanation')->default(true)->after('show_leaderboard');
            }
            
            if (!Schema::hasColumn('tryouts', 'passing_grade')) {
                $table->integer('passing_grade')->nullable()->after('show_explanation');
            }
            
            if (!Schema::hasColumn('tryouts', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('passing_grade');
            }
            
            if (!Schema::hasColumn('tryouts', 'banner')) {
                $table->string('banner')->nullable()->after('thumbnail');
            }
            
            if (!Schema::hasColumn('tryouts', 'tags')) {
                $table->json('tags')->nullable()->after('banner');
            }
            
            // 🔥 PERBAIKAN: Gunakan usersID sebagai foreign key
            if (!Schema::hasColumn('tryouts', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('tags');
            }
            
            if (!Schema::hasColumn('tryouts', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
            
            // Tambah indexes
            $table->index('status');
            $table->index('start_date');
            $table->index(['status', 'start_date']);
            $table->index('created_by');
        });
        
        // 🔥 Tambah foreign key dengan referensi ke usersID
        if (Schema::hasColumn('tryouts', 'created_by') && Schema::hasTable('users')) {
            try {
                Schema::table('tryouts', function (Blueprint $table) {
                    $table->foreign('created_by')
                          ->references('usersID')
                          ->on('users')
                          ->onDelete('set null');
                });
            } catch (\Exception $e) {
                info('Foreign key not added: ' . $e->getMessage());
            }
        }
    }

    public function down(): void
    {
        Schema::table('tryouts', function (Blueprint $table) {
            // Hapus foreign key
            try {
                $table->dropForeign(['created_by']);
            } catch (\Exception $e) {
                // Skip jika tidak ada
            }
            
            // Hapus kolom
            $columns = ['description', 'total_questions', 'status', 'start_date', 
                       'end_date', 'is_scheduled', 'is_free', 'price', 'max_attempts',
                       'show_leaderboard', 'show_explanation', 'passing_grade', 
                       'thumbnail', 'banner', 'tags', 'created_by', 'deleted_at'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('tryouts', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Hapus indexes
            $table->dropIndex(['status']);
            $table->dropIndex(['start_date']);
            $table->dropIndex(['status', 'start_date']);
            $table->dropIndex(['created_by']);
        });
    }
};