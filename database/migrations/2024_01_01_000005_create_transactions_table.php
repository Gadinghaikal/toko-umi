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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 30)->unique()->comment('Nomor invoice otomatis, contoh: INV-20240101-001');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->comment('Kasir yang melakukan transaksi');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('Total sebelum diskon');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('Nominal diskon');
            $table->decimal('tax_amount', 15, 2)->default(0)->comment('Nominal pajak (PPN)');
            $table->decimal('grand_total', 15, 2)->default(0)->comment('Total akhir yang dibayar');
            $table->decimal('amount_paid', 15, 2)->default(0)->comment('Jumlah uang yang diterima dari pelanggan');
            $table->decimal('change_amount', 15, 2)->default(0)->comment('Kembalian');
            $table->enum('payment_method', ['tunai', 'transfer', 'qris'])->default('tunai');
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date')->nullable()->comment('Waktu transaksi dilakukan');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('transaction_date');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
