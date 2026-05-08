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
        // Kita set is_verified ke TRUE agar bisa lolos pengecekan login
        $siswa = User::updateOrCreate(
            ['email' => 'ken@gmail.com'],
            [
                'name' => 'Kennedi pane',
                'phone' => '081234567890',
                'password' => Hash::make('Kennedi1'),
                'role_id' => 3,
                'is_verified' => true, // KUNCI AGAR BISA LOGIN
            ]
        );

        // 2. BUAT ATAU UPDATE PROFIL STUDENT
        Student::updateOrCreate(
            ['user_id' => $siswa->usersID],
            [
                'address' => 'Jl. Spekta No. 1',
                'parent_name' => 'Orang Tua Ken',
                'parent_phone' => '08123444555',
                'national_id_number' => '1234567890',
            ]
        );

        // 3. DAFTARKAN KE KELAS ABDI NEGARA (ID 1)
        // Kita gunakan kolom yang ada di model Enrollment ($fillable)
        Enrollment::updateOrCreate(
            [
                'user_id' => $siswa->usersID,
                'class_id' => 1
            ],
            [
                'status' => 'active', // KUNCI AGAR LANGSUNG TERDAFTAR
                'payment_proof' => 'dummy_receipt.png',
                'expires_at' => now()->addYear(),
            ]
        );

        $this->command->info('SUKSES: Akun Kennedi aktif dan sudah terdaftar di Kelas Abdi Negara!');
    }
}
