<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // Memanggil model Role yang benar

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Menghapus data lama agar tidak duplikat saat dijalankan ulang
        Role::truncate();

        Role::create(['nama_role' => 'admin']);
        Role::create(['nama_role' => 'pengajar']);
        Role::create(['nama_role' => 'siswa']);
    }
}
