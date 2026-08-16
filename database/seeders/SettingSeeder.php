<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seed pengaturan awal toko TOKO UMI.
     */
    public function run(): void
    {
        $settings = [
            // =================================================================
            // GROUP: general — Informasi Toko
            // =================================================================
            [
                'key'         => 'store_name',
                'value'       => 'TOKO UMI',
                'label'       => 'Nama Toko',
                'type'        => 'text',
                'group'       => 'general',
                'description' => 'Nama toko yang akan ditampilkan di struk dan laporan.',
            ],
            [
                'key'         => 'store_address',
                'value'       => 'Jl. Raya No. 1, Desa Contoh, Kec. Contoh, Kab. Contoh',
                'label'       => 'Alamat Toko',
                'type'        => 'textarea',
                'group'       => 'general',
                'description' => 'Alamat lengkap toko yang akan ditampilkan di struk.',
            ],
            [
                'key'         => 'store_phone',
                'value'       => '081234567890',
                'label'       => 'Nomor Telepon Toko',
                'type'        => 'text',
                'group'       => 'general',
                'description' => 'Nomor telepon/WhatsApp toko.',
            ],
            [
                'key'         => 'store_email',
                'value'       => 'tokoumi@example.com',
                'label'       => 'Email Toko',
                'type'        => 'text',
                'group'       => 'general',
                'description' => 'Alamat email toko.',
            ],
            [
                'key'         => 'store_tagline',
                'value'       => 'Sembako, Pupuk, Sparepart & Oli Motor',
                'label'       => 'Tagline / Deskripsi Singkat',
                'type'        => 'text',
                'group'       => 'general',
                'description' => 'Deskripsi singkat atau tagline toko.',
            ],

            // =================================================================
            // GROUP: pos — Pengaturan Kasir POS
            // =================================================================
            [
                'key'         => 'tax_enabled',
                'value'       => '0',
                'label'       => 'Aktifkan Pajak (PPN)',
                'type'        => 'boolean',
                'group'       => 'pos',
                'description' => 'Aktifkan atau nonaktifkan penerapan pajak PPN pada transaksi.',
            ],
            [
                'key'         => 'tax_percentage',
                'value'       => '11',
                'label'       => 'Persentase Pajak (%)',
                'type'        => 'number',
                'group'       => 'pos',
                'description' => 'Persentase PPN yang dikenakan (misal: 11 untuk 11%).',
            ],
            [
                'key'         => 'receipt_footer',
                'value'       => 'Terima kasih telah berbelanja di TOKO UMI. Barang yang sudah dibeli tidak dapat dikembalikan.',
                'label'       => 'Pesan Footer Struk',
                'type'        => 'textarea',
                'group'       => 'pos',
                'description' => 'Pesan yang ditampilkan di bagian bawah struk penjualan.',
            ],
            [
                'key'         => 'receipt_show_cashier',
                'value'       => '1',
                'label'       => 'Tampilkan Nama Kasir di Struk',
                'type'        => 'boolean',
                'group'       => 'pos',
                'description' => 'Tampilkan atau sembunyikan nama kasir pada struk.',
            ],
            [
                'key'         => 'default_payment_method',
                'value'       => 'tunai',
                'label'       => 'Metode Pembayaran Default',
                'type'        => 'text',
                'group'       => 'pos',
                'description' => 'Metode pembayaran yang dipilih secara default saat transaksi (tunai/transfer/qris).',
            ],

            // =================================================================
            // GROUP: report — Pengaturan Laporan
            // =================================================================
            [
                'key'         => 'report_show_profit',
                'value'       => '1',
                'label'       => 'Tampilkan Kolom Profit di Laporan',
                'type'        => 'boolean',
                'group'       => 'report',
                'description' => 'Tampilkan atau sembunyikan kalkulasi profit di halaman laporan.',
            ],
            [
                'key'         => 'report_currency_symbol',
                'value'       => 'Rp',
                'label'       => 'Simbol Mata Uang',
                'type'        => 'text',
                'group'       => 'report',
                'description' => 'Simbol mata uang yang digunakan di laporan dan struk (contoh: Rp).',
            ],

            // =================================================================
            // GROUP: appearance — Tampilan
            // =================================================================
            [
                'key'         => 'sidebar_color',
                'value'       => 'dark',
                'label'       => 'Warna Sidebar',
                'type'        => 'text',
                'group'       => 'appearance',
                'description' => 'Tema warna sidebar aplikasi (dark/light).',
            ],
        ];

        foreach ($settings as $settingData) {
            Setting::updateOrCreate(
                ['key' => $settingData['key']],
                $settingData
            );
        }

        $this->command->info('✅ SettingSeeder: 13 pengaturan toko berhasil dibuat.');
    }
}
