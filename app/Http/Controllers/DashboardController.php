<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\StockHistory;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan statistik lengkap.
     */
    public function index(): View
    {
        $today     = Carbon::today();
        $thisMonth = Carbon::now();

        // =====================================================================
        // STATISTIK UTAMA (4 kartu atas)
        // =====================================================================

        // 1. Total penjualan hari ini
        $todayRevenue = Transaction::completed()
            ->whereDate('transaction_date', $today)
            ->sum('grand_total');

        // 2. Jumlah transaksi hari ini
        $todayTransactionCount = Transaction::completed()
            ->whereDate('transaction_date', $today)
            ->count();

        // 3. Total penjualan bulan ini
        $monthRevenue = Transaction::completed()
            ->whereYear('transaction_date', $thisMonth->year)
            ->whereMonth('transaction_date', $thisMonth->month)
            ->sum('grand_total');

        // 4. Total produk aktif
        $totalProducts = Product::where('is_active', true)->count();

        // =====================================================================
        // PERINGATAN STOK
        // =====================================================================

        // Produk stok menipis (stok <= min_stock dan stok > 0)
        $lowStockProducts = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->take(5)
            ->get();

        // Jumlah produk stok habis
        $outOfStockCount = Product::where('is_active', true)
            ->where('stock', '<=', 0)
            ->count();

        // Jumlah produk stok menipis
        $lowStockCount = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        // =====================================================================
        // TRANSAKSI TERBARU (5 terakhir)
        // =====================================================================
        $recentTransactions = Transaction::with('user')
            ->completed()
            ->orderByDesc('transaction_date')
            ->take(5)
            ->get();

        // =====================================================================
        // PRODUK TERLARIS HARI INI
        // =====================================================================
        $topProductsToday = \DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->whereDate('transactions.transaction_date', $today)
            ->where('transactions.status', 'completed')
            ->select(
                'transaction_items.product_name',
                \DB::raw('SUM(transaction_items.quantity) as total_qty'),
                \DB::raw('SUM(transaction_items.subtotal) as total_revenue')
            )
            ->groupBy('transaction_items.product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // =====================================================================
        // GRAFIK PENDAPATAN 7 HARI TERAKHIR
        // =====================================================================
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Transaction::completed()
                ->whereDate('transaction_date', $date)
                ->sum('grand_total');

            $last7Days->push([
                'date'    => $date->format('d M'),
                'revenue' => (float) $revenue,
            ]);
        }

        // =====================================================================
        // PENDAPATAN BULAN INI VS BULAN LALU
        // =====================================================================
        $lastMonthRevenue = Transaction::completed()
            ->whereYear('transaction_date', Carbon::now()->subMonth()->year)
            ->whereMonth('transaction_date', Carbon::now()->subMonth()->month)
            ->sum('grand_total');

        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
        }

        return view('dashboard', compact(
            'todayRevenue',
            'todayTransactionCount',
            'monthRevenue',
            'totalProducts',
            'lowStockProducts',
            'outOfStockCount',
            'lowStockCount',
            'recentTransactions',
            'topProductsToday',
            'last7Days',
            'lastMonthRevenue',
            'revenueGrowth'
        ));
    }
}
