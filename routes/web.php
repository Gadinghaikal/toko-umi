<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — TOKO UMI
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard (jika sudah login) atau login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =========================================================================
// ROUTES YANG MEMBUTUHKAN AUTH + ACTIVE ACCOUNT
// =========================================================================
Route::middleware(['auth', 'is.active'])->group(function () {

    // ------ DASHBOARD ------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ------ PROFILE (dari Breeze) ------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================
    // TAHAP 3: MASTER DATA (akan diimplementasi di Tahap 3)
    // =========================================================
    Route::resource('categories', \App\Http\Controllers\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\ProductController::class);

    // =========================================================
    // TAHAP 3: INVENTARIS (akan diimplementasi di Tahap 3)
    // =========================================================
    Route::get('/stock/history', [\App\Http\Controllers\StockController::class, 'history'])->name('stock.history');
    Route::get('/stock/adjustments', [\App\Http\Controllers\StockController::class, 'adjustments'])->name('stock.adjustments');
    Route::post('/stock/adjustments', [\App\Http\Controllers\StockController::class, 'storeAdjustment'])->name('stock.adjustments.store');
    Route::post('/products/{product}/add-stock', [\App\Http\Controllers\StockController::class, 'addStock'])->name('products.add-stock');

    // =========================================================
    // TAHAP 4: KASIR / POS (akan diimplementasi di Tahap 4)
    // =========================================================
    Route::get('/kasir', [\App\Http\Controllers\KasirController::class, 'index'])->name('kasir.index');
    Route::post('/kasir/transaction', [\App\Http\Controllers\KasirController::class, 'store'])->name('kasir.transaction.store');

    // =========================================================
    // TAHAP 4: TRANSAKSI (akan diimplementasi di Tahap 4)
    // =========================================================
    Route::get('/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/print', [\App\Http\Controllers\TransactionController::class, 'print'])->name('transactions.print');

    // =========================================================
    // TAHAP 5: LAPORAN (Admin Only — akan diimplementasi di Tahap 5)
    // =========================================================
    Route::middleware('admin')->group(function () {
        Route::get('/reports/daily', [\App\Http\Controllers\ReportController::class, 'daily'])->name('reports.daily');
        Route::get('/reports/monthly', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('reports.monthly');
        Route::get('/reports/yearly', [\App\Http\Controllers\ReportController::class, 'yearly'])->name('reports.yearly');
        Route::get('/reports/best-selling', [\App\Http\Controllers\ReportController::class, 'bestSelling'])->name('reports.best-selling');
        Route::get('/reports/stock', [\App\Http\Controllers\ReportController::class, 'stock'])->name('reports.stock');
        Route::get('/reports/export/pdf/{type}', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
        Route::get('/reports/export/excel/{type}', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export.excel');

        // =====================================================
        // TAHAP 5: MANAJEMEN USER (Admin Only)
        // =====================================================
        Route::resource('users', \App\Http\Controllers\UserController::class);

        // =====================================================
        // TAHAP 5: PENGATURAN TOKO (Admin Only)
        // =====================================================
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
    });

});

require __DIR__ . '/auth.php';
