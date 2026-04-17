<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\ClassModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// class DatabaseSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // Panggil RoleSeeder dulu
//         $this->call(RoleSeeder::class);

//         // Ambil data role yang sudah dibuat
//         $adminRole = Role::where('name', 'admin')->first();
//         $guruRole  = Role::where('name', 'pengajar')->first();

//         // 2. Buat User ADMIN
//         User::create([
//             'name' => 'Admin Spekta',
//             'email' => 'admin@gmail.com',
//             'password' => bcrypt('password123'),
//             'role_id' => $adminRole->rolesID,
//             'phone' => '08123456789'
//         ]);

//         // 3. Buat User PENGAJAR
//         User::create([
//             'name' => 'Pak Guru Spekta',
//             'email' => 'guru@gmail.com',
//             'password' => bcrypt('password123'),
//             'role_id' => $guruRole->rolesID,
//             'phone' => '08123456788'
//         ]);



//         $programs = [
//             ['program_name' => 'CALON ABDI NEGARA', 'image' => 'abdi_negara.png'],
//             ['program_name' => 'PTN & UNHAN', 'image' => 'ptn.png'],
//             ['program_name' => 'SMA & SMP REGULER', 'image' => 'reguler.png'],
//             ['program_name' => 'SMA FAVORIT', 'image' => 'favorit.png'],
//         ];

//         foreach ($programs as $program) {
//             ClassModel::create($program);
//         }

//         $this->call(ClassContentSeeder::class);
//     }
// }
