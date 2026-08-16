<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    /**
     * Tampilkan antarmuka POS / Kasir.
     */
    public function index(Request $request)
    {
        // Ambil semua produk aktif yang stoknya > 0
        $products = Product::with('category')
            ->active()
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        $categories = Category::active()->orderBy('name')->get();

        return view('kasir.index', compact('products', 'categories'));
    }

    /**
     * Proses transaksi dari Kasir.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|in:tunai,transfer,qris',
            'total_amount' => 'required|numeric|min:1',
            'paid_amount' => 'required|numeric|min:0',
            'cart' => 'required|json', // Keranjang dikirim sebagai string JSON
        ]);

        $cart = json_decode($request->cart, true);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong. Tidak dapat memproses transaksi.');
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            
            // 1. Buat Transaksi
            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'notes' => 'Pelanggan: ' . ($request->customer_name ?: 'Umum'),
                // invoice_number akan digenerate otomatis oleh model boot()
                'subtotal' => 0,
                'grand_total' => 0, // Akan diupdate setelah hitung item
                'amount_paid' => $request->paid_amount,
                'payment_method' => $request->payment_method,
            ]);

            // 2. Loop setiap item di keranjang
            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['id']);
                
                $qty = (int) $item['qty'];
                if ($qty <= 0) continue;

                // Cek ketersediaan stok
                if ($product->stock < $qty) {
                    throw new \Exception("Stok tidak mencukupi untuk barang: {$product->name}. Sisa stok: {$product->stock}");
                }

                $subtotal = $product->selling_price * $qty;
                $totalAmount += $subtotal;

                // 2a. Buat Transaction Item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'unit' => $product->unit,
                    'selling_price' => $product->selling_price,
                    'purchase_price' => $product->purchase_price,
                    'quantity' => $qty,
                ]);

                // 2b. Kurangi Stok Produk
                $stockBefore = $product->stock;
                $stockAfter = $stockBefore - $qty;
                $product->update(['stock' => $stockAfter]);

                // 2c. Catat Riwayat Stok (Stock Out)
                StockHistory::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'out',
                    'quantity_before' => $stockBefore,
                    'quantity_change' => -$qty,
                    'quantity_after' => $stockAfter,
                    'reference' => $transaction->invoice_number,
                    'notes' => 'Penjualan kasir'
                ]);
            }

            // Validasi uang bayar (hanya untuk tunai, yang lain dianggap lunas)
            if ($request->payment_method === 'tunai' && $request->paid_amount < $totalAmount) {
                 throw new \Exception("Uang pembayaran kurang! Total: Rp " . number_format($totalAmount, 0, ',', '.') . " | Dibayar: Rp " . number_format($request->paid_amount, 0, ',', '.'));
            }

            // Hitung kembalian
            $changeAmount = 0;
            if ($request->payment_method === 'tunai' && $request->paid_amount > $totalAmount) {
                $changeAmount = $request->paid_amount - $totalAmount;
            }

            // 3. Update total akhir transaksi
            $transaction->update([
                'subtotal' => $totalAmount,
                'grand_total' => $totalAmount,
                'change_amount' => $changeAmount
            ]);

            DB::commit();

            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Transaksi gagal: ' . $e->getMessage());
        }
    }
}
