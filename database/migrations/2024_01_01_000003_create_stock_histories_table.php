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
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('type', ['in', 'out', 'adjustment'])->comment('in=masuk (pembelian), out=keluar (penjualan), adjustment=penyesuaian');
            $table->integer('quantity_before')->comment('Stok sebelum perubahan');
            $table->integer('quantity_change')->comment('Jumlah perubahan (positif=masuk, negatif=keluar)');
            $table->integer('quantity_after')->comment('Stok setelah perubahan');
            $table->decimal('purchase_price', 15, 2)->nullable()->comment('Harga beli saat penambahan stok');
            $table->string('reference', 100)->nullable()->comment('Referensi: nomor transaksi atau nomor penyesuaian');
            $table->text('notes')->nullable()->comment('Catatan tambahan');
            $table->timestamps();

            $table->index(['product_id', 'type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_histories');
    }
};
