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

       $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $guruRole  = Role::firstOrCreate(['name' => 'pengajar']);
        $siswaRole = Role::firstOrCreate(['name' => 'siswa']);
    }
}
