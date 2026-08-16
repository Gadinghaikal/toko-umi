<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_code', 30)->unique()->comment('Kode penyesuaian otomatis, contoh: ADJ-20240101-001');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('stock_before')->comment('Stok sebelum penyesuaian');
            $table->integer('stock_after')->comment('Stok setelah penyesuaian (input dari user)');
            $table->integer('difference')->comment('Selisih: stock_after - stock_before');
            $table->enum('reason', [
                'koreksi_fisik',
                'barang_rusak',
                'barang_kadaluarsa',
                'kesalahan_input',
                'lainnya'
            ])->default('koreksi_fisik')->comment('Alasan penyesuaian stok');
            $table->text('notes')->nullable()->comment('Catatan detail penyesuaian');
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
