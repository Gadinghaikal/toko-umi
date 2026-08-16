<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed akun pengguna awal: 1 Admin dan 1 Kasir.
     */
    public function run(): void
    {
        // =====================================================================
        // Admin
        // =====================================================================
        User::updateOrCreate(
            ['email' => 'admin@tokoumi.com'],
            [
                'name'      => 'Administrator',
                'email'     => 'admin@tokoumi.com',
                'password'  => Hash::make('admin123'),
                'role'      => 'admin',
                'is_active' => true,
                'phone'     => '081234567890',
            ]
        );

        // =====================================================================
        // Kasir
        // =====================================================================
        User::updateOrCreate(
            ['email' => 'kasir@tokoumi.com'],
            [
                'name'      => 'Kasir Utama',
                'email'     => 'kasir@tokoumi.com',
                'password'  => Hash::make('kasir123'),
                'role'      => 'kasir',
                'is_active' => true,
                'phone'     => '089876543210',
            ]
        );

        $this->command->info('✅ UserSeeder: 2 akun berhasil dibuat (1 Admin, 1 Kasir).');
    }
}
