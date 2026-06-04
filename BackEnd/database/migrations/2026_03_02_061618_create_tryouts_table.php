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
        Schema::create('tryouts', function (Blueprint $table) {
            // ============================================================
            // PRIMARY KEY
            // ============================================================
            $table->id('tryout_id');

            // ============================================================
            // RELATIONSHIPS
            // ============================================================
            // Referensi ke tabel classes
            $table->foreignId('class_id')
                  ->constrained('classes', 'class_id')
                  ->onDelete('cascade');

            // Siapa yang membuat paket tryout (admin/guru)
            // MODIFIKASI: Mengubah referensi dari 'id' ke 'usersID'
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users', 'usersID')
                  ->onDelete('set null');

            // ============================================================
            // INFORMASI DASAR TRYOUT
            // ============================================================
            $table->string('title', 255);                 // Judul tryout
            $table->text('description')->nullable();       // Deskripsi tryout
            $table->string('slug')->unique()->nullable();  // URL-friendly title

            // ============================================================
            // MATA PELAJARAN (untuk grouping soal per mapel)
            // ============================================================
            $table->string('subject_category')->nullable(); // Bisa diisi: 'UTBK' atau null
            $table->json('subjects')->nullable();           // Array mata pelajaran yang tersedia
            // Contoh: ["Matematika", "Bahasa Inggris", "Fisika", "Kimia"]

            // ============================================================
            // DURASI DAN JUMLAH SOAL
            // ============================================================
            $table->integer('duration_minutes')->default(120); // Durasi dalam menit
            $table->integer('total_questions')->default(0);     // Total soal (auto update)

            // ============================================================
            // STATUS TRYOUT
            // ============================================================
            $table->enum('status', [
                'draft',        // Masih draft, belum bisa diakses siswa
                'published',    // Sudah dipublish, bisa diakses
                'ongoing',      // Sedang berlangsung
                'completed',    // Selesai
                'archived'      // Diarsipkan
            ])->default('draft');

            // ============================================================
            // JADWAL TRYOUT (opsional, untuk tryout terjadwal)
            // ============================================================
            $table->dateTime('start_date')->nullable();   // Kapan tryout dimulai
            $table->dateTime('end_date')->nullable();     // Kapan tryout berakhir
            $table->boolean('is_scheduled')->default(false); // Tryout terjadwal atau mandiri

            // ============================================================
            // TRYOUT SETTINGS
            // ============================================================
            $table->boolean('is_free')->default(true);     // Gratis atau berbayar
            $table->decimal('price', 10, 2)->default(0);   // Harga jika berbayar
            $table->integer('max_attempts')->default(1);   // Maksimal percobaan
            $table->boolean('show_leaderboard')->default(true); // Tampilkan peringkat
            $table->boolean('show_explanation')->default(true); // Tampilkan pembahasan

            // ============================================================
            // PASSING GRADE (opsional)
            // ============================================================
            $table->integer('passing_grade')->nullable();   // Minimal nilai lulus

            // ============================================================
            // METADATA
            // ============================================================
            $table->string('thumbnail')->nullable();       // Gambar thumbnail tryout
            $table->string('banner')->nullable();          // Banner tryout
            $table->json('tags')->nullable();              // Tags: ['UTBK', 'SNBT', 'Mandiri']

            // ============================================================
            // TIMESTAMPS
            // ============================================================
            $table->timestamps();                          // created_at, updated_at
            $table->softDeletes();                         // deleted_at (jika perlu hapus soft)

            // ============================================================
            // INDEXES (untuk performa query)
            // ============================================================
            $table->index('class_id');
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('is_scheduled');
            $table->index(['status', 'start_date']);       // Composite index
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tryouts');
    }
};
