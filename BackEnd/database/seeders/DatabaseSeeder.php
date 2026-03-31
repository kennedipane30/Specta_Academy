<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Role Sesuai ERD
        $adminRole = Role::create(['name' => 'admin']);
        $guruRole  = Role::create(['name' => 'pengajar']);
        $siswaRole = Role::create(['name' => 'siswa']);

        // 2. Buat User ADMIN
        User::create([
            'name' => 'Admin Spekta',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $adminRole->rolesID,
            'phone' => '08123456789'
        ]);

        // 3. Buat User PENGAJAR
        User::create([
            'name' => 'Pak Guru Spekta',
            'email' => 'guru@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $guruRole->rolesID,
            'phone' => '08123456788'
        ]);

        // 4. Isi data 4 Program Utama Spekta
        $programs = [
            ['nama_program' => 'CALON ABDI NEGARA', 'gambar' => 'abdi_negara.png'],
            ['nama_program' => 'PTN & UNHAN', 'gambar' => 'ptn.png'],
            ['nama_program' => 'SMA & SMP REGULER', 'gambar' => 'reguler.png'],
            ['nama_program' => 'SMA FAVORIT', 'gambar' => 'favorit.png'],
        ];

        foreach ($programs as $program) {
            ClassModel::create($program);
        }

        // --- MODIFIKASI: PANGGIL SEEDER MATERI DI SINI ---
        // Ini menjamin Kelas sudah ada sebelum Materi dibuat
        $this->call(ClassContentSeeder::class);
    }
}
