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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->string('product_name', 200)->comment('Snapshot nama produk saat transaksi');
            $table->string('product_code', 20)->comment('Snapshot kode produk saat transaksi');
            $table->string('unit', 30)->comment('Snapshot satuan produk saat transaksi');
            $table->decimal('selling_price', 15, 2)->comment('Snapshot harga jual saat transaksi');
            $table->decimal('purchase_price', 15, 2)->comment('Snapshot harga beli saat transaksi (untuk laporan profit)');
            $table->integer('quantity');
            $table->decimal('discount_per_item', 15, 2)->default(0)->comment('Diskon per item');
            $table->decimal('subtotal', 15, 2)->comment('(selling_price - discount_per_item) * quantity');
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
