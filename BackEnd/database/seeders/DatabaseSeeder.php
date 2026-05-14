<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil seeder yang sudah Anda buat sebelumnya
        $this->call([
            SiswaAbdiNegaraSeeder::class,
        ]);
    }
}
