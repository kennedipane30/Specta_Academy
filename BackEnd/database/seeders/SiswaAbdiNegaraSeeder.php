<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

class SiswaAbdiNegaraSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT ATAU UPDATE AKUN USER
        $siswa = User::updateOrCreate(
            ['email' => 'ken@gmail.com'],
            [
                'name' => 'Kennedi pane',
                'phone' => '081234567890',
                'password' => Hash::make('Kennedi1'),
                'role_id' => 3,
                'is_verified' => true, // Agar bisa login
            ]
        );

        // 2. BUAT ATAU UPDATE PROFIL STUDENT
        // MODIFIKASI: Menambahkan class_id agar tidak null di pgAdmin
        Student::updateOrCreate(
            ['user_id' => $siswa->usersID],
            [
                'class_id' => 1, // ✨ MEMASTIKAN SISWA TERHUBUNG KE KELAS 1
                'address' => 'Jl. Spekta No. 1',
                'parent_name' => 'Orang Tua Ken',
                'parent_phone' => '08123444555',
                'national_id_number' => '1234567890',
            ]
        );

        // 3. DAFTARKAN KE KELAS ABDI NEGARA (ID 1)
        Enrollment::updateOrCreate(
            [
                'user_id' => $siswa->usersID,
                'class_id' => 1 // ✨ DAFTAR KE KELAS 1
            ],
            [
                'status' => 'active', // Langsung aktif agar tombol "Mulai Belajar" muncul
                'payment_proof' => 'dummy_receipt.png',
                'expires_at' => now()->addYear(),
            ]
        );

        $this->command->info('SUKSES: Akun Kennedi aktif dan class_id disetel ke 1!');
    }
}
