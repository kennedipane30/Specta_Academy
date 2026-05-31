<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // ============================================================
            // RELATION KE TRYOUT
            // ============================================================
            if (!Schema::hasColumn('questions', 'tryout_id')) {
                $table->foreignId('tryout_id')
                      ->after('question_id')
                      ->constrained('tryouts', 'tryout_id')
                      ->onDelete('cascade');
            }
            
            // ============================================================
            // MATA PELAJARAN
            // ============================================================
            if (!Schema::hasColumn('questions', 'subject')) {
                $table->string('subject', 100)->nullable()->after('tryout_id');
            }
            
            // ============================================================
            // NOMOR SOAL
            // ============================================================
            if (!Schema::hasColumn('questions', 'question_number')) {
                $table->integer('question_number')->default(0)->after('subject');
            }
            
            // ============================================================
            // GAMBAR PERTANYAAN
            // ============================================================
            if (!Schema::hasColumn('questions', 'question_image')) {
                $table->string('question_image', 255)->nullable()->after('question');
            }
            
            if (!Schema::hasColumn('questions', 'option_e')) {
                $table->string('option_e')->nullable()->after('option_d');
            }
            
            // ============================================================
            // GAMBAR OPSI JAWABAN
            // ============================================================
            if (!Schema::hasColumn('questions', 'option_a_image')) {
                $table->string('option_a_image', 255)->nullable()->after('option_a');
            }
            
            if (!Schema::hasColumn('questions', 'option_b_image')) {
                $table->string('option_b_image', 255)->nullable()->after('option_b');
            }
            
            if (!Schema::hasColumn('questions', 'option_c_image')) {
                $table->string('option_c_image', 255)->nullable()->after('option_c');
            }
            
            if (!Schema::hasColumn('questions', 'option_d_image')) {
                $table->string('option_d_image', 255)->nullable()->after('option_d');
            }
            
            // ============================================================
            // PEMBAHASAN
            // ============================================================
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable()->after('correct_answer');
            }
            
            // ============================================================
            // POIN PER SOAL (default 1, bisa untuk soal sulit diberi poin lebih)
            // ============================================================
            if (!Schema::hasColumn('questions', 'points')) {
                $table->integer('points')->default(1)->after('explanation');
            }
            
            // ============================================================
            // INDEXES (untuk performa query)
            // ============================================================
            $table->index('tryout_id');
            $table->index('subject');
            $table->index('question_number');
            $table->index(['tryout_id', 'question_number']); // Composite index
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            if (Schema::hasColumn('questions', 'tryout_id')) {
                $table->dropForeign(['tryout_id']);
            }
            
            // Hapus kolom-kolom yang ditambahkan
            $columns = [
                'tryout_id',
                'subject',
                'question_number',
                'question_image',
                'option_e',
                'option_a_image',
                'option_b_image',
                'option_c_image',
                'option_d_image',
                'explanation',
                'points'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('questions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};