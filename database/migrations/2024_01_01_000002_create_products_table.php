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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Kode produk otomatis, contoh: PRD-0001');
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name', 200);
            $table->string('unit', 30)->comment('Satuan: pcs, kg, liter, karung, botol, dll');
            $table->decimal('purchase_price', 15, 2)->default(0)->comment('Harga beli / harga modal');
            $table->decimal('selling_price', 15, 2)->default(0)->comment('Harga jual');
            $table->integer('stock')->default(0)->comment('Stok saat ini');
            $table->integer('min_stock')->default(5)->comment('Stok minimum (alert)');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['category_id', 'is_active']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
