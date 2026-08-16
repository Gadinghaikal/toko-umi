<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Urutan seeding penting karena ada foreign key dependencies.
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai proses seeding database TOKO UMI...');
        $this->command->newLine();

        // 1. Seeding users (tidak ada dependency)
        $this->call(UserSeeder::class);

        // 2. Seeding categories (tidak ada dependency)
        $this->call(CategorySeeder::class);

        // 3. Seeding settings (tidak ada dependency)
        $this->call(SettingSeeder::class);

        // 4. Seeding products (bergantung pada categories)
        $this->call(ProductSeeder::class);

        $this->command->newLine();
        $this->command->info('✨ Seeding selesai! Database TOKO UMI siap digunakan.');
        $this->command->newLine();
        $this->command->table(
            ['Akun', 'Email', 'Password', 'Role'],
            [
                ['Administrator', 'admin@tokoumi.com', 'admin123', 'admin'],
                ['Kasir Utama',   'kasir@tokoumi.com', 'kasir123', 'kasir'],
            ]
        );
    }
}
