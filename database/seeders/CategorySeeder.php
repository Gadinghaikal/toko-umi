<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed 4 kategori utama toko TOKO UMI.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Sembako',
                'slug'        => 'sembako',
                'description' => 'Kebutuhan sembilan bahan pokok: beras, gula, minyak goreng, terigu, garam, dan lainnya.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Pupuk Pertanian',
                'slug'        => 'pupuk-pertanian',
                'description' => 'Berbagai jenis pupuk untuk kebutuhan pertanian: pupuk urea, NPK, organik, dan pestisida.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Sparepart Motor',
                'slug'        => 'sparepart-motor',
                'description' => 'Suku cadang dan aksesori sepeda motor: kampas rem, filter udara, busi, rantai, dan lainnya.',
                'is_active'   => true,
            ],
            [
                'name'        => 'Oli Motor',
                'slug'        => 'oli-motor',
                'description' => 'Berbagai merek dan tipe oli pelumas mesin sepeda motor.',
                'is_active'   => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('✅ CategorySeeder: 4 kategori berhasil dibuat (Sembako, Pupuk Pertanian, Sparepart Motor, Oli Motor).');
    }
}
