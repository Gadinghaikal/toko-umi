<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed 16 produk contoh untuk TOKO UMI (4 per kategori).
     */
    public function run(): void
    {
        // Ambil semua kategori berdasarkan slug
        $sembako       = Category::where('slug', 'sembako')->first();
        $pupuk         = Category::where('slug', 'pupuk-pertanian')->first();
        $sparepart     = Category::where('slug', 'sparepart-motor')->first();
        $oli           = Category::where('slug', 'oli-motor')->first();

        if (!$sembako || !$pupuk || !$sparepart || !$oli) {
            $this->command->warn('⚠️  ProductSeeder: Pastikan CategorySeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        // =====================================================================
        // PRODUK SEMBAKO (4 produk)
        // =====================================================================
        $sembakoProducts = [
            [
                'category_id'    => $sembako->id,
                'name'           => 'Beras Premium 5kg',
                'unit'           => 'karung',
                'purchase_price' => 60000,
                'selling_price'  => 70000,
                'stock'          => 50,
                'min_stock'      => 10,
                'description'    => 'Beras putih premium kualitas terbaik, 5kg per karung.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $sembako->id,
                'name'           => 'Gula Pasir 1kg',
                'unit'           => 'kg',
                'purchase_price' => 14000,
                'selling_price'  => 16000,
                'stock'          => 100,
                'min_stock'      => 20,
                'description'    => 'Gula pasir putih halus, kemasan 1kg.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $sembako->id,
                'name'           => 'Minyak Goreng Bimoli 2L',
                'unit'           => 'botol',
                'purchase_price' => 30000,
                'selling_price'  => 34000,
                'stock'          => 60,
                'min_stock'      => 12,
                'description'    => 'Minyak goreng serbaguna Bimoli, kemasan botol 2 liter.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $sembako->id,
                'name'           => 'Tepung Terigu Segitiga Biru 1kg',
                'unit'           => 'bungkus',
                'purchase_price' => 12000,
                'selling_price'  => 14000,
                'stock'          => 80,
                'min_stock'      => 15,
                'description'    => 'Tepung terigu protein tinggi, kemasan 1kg.',
                'is_active'      => true,
            ],
        ];

        // =====================================================================
        // PRODUK PUPUK PERTANIAN (4 produk)
        // =====================================================================
        $pupukProducts = [
            [
                'category_id'    => $pupuk->id,
                'name'           => 'Pupuk Urea 50kg',
                'unit'           => 'karung',
                'purchase_price' => 250000,
                'selling_price'  => 270000,
                'stock'          => 30,
                'min_stock'      => 5,
                'description'    => 'Pupuk Urea bersubsidi, kemasan 50kg per karung.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $pupuk->id,
                'name'           => 'Pupuk NPK Phonska 50kg',
                'unit'           => 'karung',
                'purchase_price' => 290000,
                'selling_price'  => 315000,
                'stock'          => 25,
                'min_stock'      => 5,
                'description'    => 'Pupuk NPK Phonska bersubsidi, kemasan 50kg per karung.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $pupuk->id,
                'name'           => 'Pupuk Organik Petroganik 40kg',
                'unit'           => 'karung',
                'purchase_price' => 80000,
                'selling_price'  => 95000,
                'stock'          => 40,
                'min_stock'      => 8,
                'description'    => 'Pupuk organik granul Petroganik, kemasan 40kg.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $pupuk->id,
                'name'           => 'Pestisida Decis 100ml',
                'unit'           => 'botol',
                'purchase_price' => 28000,
                'selling_price'  => 35000,
                'stock'          => 50,
                'min_stock'      => 10,
                'description'    => 'Insektisida Decis EC 25 untuk mengatasi hama, kemasan 100ml.',
                'is_active'      => true,
            ],
        ];

        // =====================================================================
        // PRODUK SPAREPART MOTOR (4 produk)
        // =====================================================================
        $sparepartProducts = [
            [
                'category_id'    => $sparepart->id,
                'name'           => 'Kampas Rem Depan Honda Beat',
                'unit'           => 'set',
                'purchase_price' => 28000,
                'selling_price'  => 38000,
                'stock'          => 20,
                'min_stock'      => 5,
                'description'    => 'Kampas rem cakram depan untuk Honda Beat, berkualitas original.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $sparepart->id,
                'name'           => 'Filter Udara Honda Vario 125',
                'unit'           => 'pcs',
                'purchase_price' => 22000,
                'selling_price'  => 30000,
                'stock'          => 15,
                'min_stock'      => 3,
                'description'    => 'Filter udara original untuk Honda Vario 125.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $sparepart->id,
                'name'           => 'Busi NGK CPR8EA-9',
                'unit'           => 'pcs',
                'purchase_price' => 20000,
                'selling_price'  => 28000,
                'stock'          => 30,
                'min_stock'      => 8,
                'description'    => 'Busi NGK tipe CPR8EA-9, cocok untuk berbagai motor matic.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $sparepart->id,
                'name'           => 'Rantai Motor RK 428H-110L',
                'unit'           => 'pcs',
                'purchase_price' => 65000,
                'selling_price'  => 85000,
                'stock'          => 10,
                'min_stock'      => 3,
                'description'    => 'Rantai motor RK standar 428H-110 link, untuk berbagai motor bebek.',
                'is_active'      => true,
            ],
        ];

        // =====================================================================
        // PRODUK OLI MOTOR (4 produk)
        // =====================================================================
        $oliProducts = [
            [
                'category_id'    => $oli->id,
                'name'           => 'Oli Motul 5100 10W-40 1L',
                'unit'           => 'botol',
                'purchase_price' => 75000,
                'selling_price'  => 90000,
                'stock'          => 24,
                'min_stock'      => 6,
                'description'    => 'Oli mesin 4T semi-sintetik Motul 5100, 10W-40, 1 liter.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $oli->id,
                'name'           => 'Oli Yamalube Super Matic 10W-40 1L',
                'unit'           => 'botol',
                'purchase_price' => 45000,
                'selling_price'  => 58000,
                'stock'          => 30,
                'min_stock'      => 8,
                'description'    => 'Oli mesin resmi Yamaha untuk motor matic, 10W-40, 1 liter.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $oli->id,
                'name'           => 'Oli AHM MPX1 10W-30 1L',
                'unit'           => 'botol',
                'purchase_price' => 42000,
                'selling_price'  => 55000,
                'stock'          => 36,
                'min_stock'      => 10,
                'description'    => 'Oli mesin resmi Honda untuk motor matic, 10W-30, 1 liter.',
                'is_active'      => true,
            ],
            [
                'category_id'    => $oli->id,
                'name'           => 'Oli Federal Super Matic Sae 10W-30 0.8L',
                'unit'           => 'botol',
                'purchase_price' => 30000,
                'selling_price'  => 40000,
                'stock'          => 40,
                'min_stock'      => 10,
                'description'    => 'Oli Federal untuk motor matic, SAE 10W-30, kemasan 0.8 liter.',
                'is_active'      => true,
            ],
        ];

        // =====================================================================
        // Gabung semua produk dan simpan ke database
        // =====================================================================
        $allProducts = array_merge(
            $sembakoProducts,
            $pupukProducts,
            $sparepartProducts,
            $oliProducts
        );

        $count = 0;
        foreach ($allProducts as $productData) {
            // Cek apakah produk sudah ada berdasarkan nama dan kategori
            $existingProduct = Product::where('name', $productData['name'])
                ->where('category_id', $productData['category_id'])
                ->first();

            if (!$existingProduct) {
                // Buat produk baru — kode produk akan di-generate otomatis oleh boot()
                Product::create($productData);
                $count++;
            }
        }

        $this->command->info("✅ ProductSeeder: {$count} produk baru berhasil dibuat (16 total, 4 per kategori).");
    }
}
