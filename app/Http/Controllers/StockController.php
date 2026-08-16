<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
    /**
     * Display a listing of stock history.
     */
    public function history(Request $request)
    {
        $query = StockHistory::with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        $histories = $query->latest()->paginate(15)->withQueryString();

        return view('stock.history', compact('histories'));
    }

    /**
     * Display a listing of stock adjustments and the form to create one.
     */
    public function adjustments(Request $request)
    {
        $query = StockAdjustment::with(['product', 'user']);
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            })->orWhere('adjustment_code', 'like', '%' . $search . '%');
        }

        $adjustments = $query->latest()->paginate(15)->withQueryString();
        
        // Data for adjustment modal
        $products = Product::active()->orderBy('name')->get();
        $reasons = StockAdjustment::REASONS;
        
        // Auto-select product if passed in query string (from product index)
        $selectedProductId = $request->product_id;

        return view('stock.adjustments', compact('adjustments', 'products', 'reasons', 'selectedProductId'));
    }

    /**
     * Store a newly created stock adjustment.
     */
    public function storeAdjustment(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'stock_after' => 'required|integer|min:0',
            'reason' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($request->product_id);
            $stockBefore = $product->stock;
            $stockAfter = $request->stock_after;
            $difference = $stockAfter - $stockBefore;

            if ($difference == 0) {
                return back()->with('info', 'Tidak ada perubahan stok (Stok lama dan baru sama).');
            }

            // 1. Catat Stock Adjustment
            $adjustment = StockAdjustment::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                // difference dihitung otomatis di model boot
                'reason' => $request->reason,
                'notes' => $request->notes
            ]);

            // 2. Catat Stock History
            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'adjustment',
                'quantity_before' => $stockBefore,
                'quantity_change' => $difference,
                'quantity_after' => $stockAfter,
                'reference' => $adjustment->adjustment_code,
                'notes' => 'Penyesuaian stok: ' . $adjustment->reason_label
            ]);

            // 3. Update Product Stock
            $product->update(['stock' => $stockAfter]);

            DB::commit();

            return redirect()->route('stock.adjustments')->with('success', 'Penyesuaian stok berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Add stock quickly from products page (Stock In)
     */
    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'purchase_price' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $stockBefore = $product->stock;
            $added = $request->quantity;
            $stockAfter = $stockBefore + $added;

            // 1. Catat Stock History
            StockHistory::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity_before' => $stockBefore,
                'quantity_change' => $added,
                'quantity_after' => $stockAfter,
                'purchase_price' => $request->purchase_price ?? $product->purchase_price,
                'reference' => 'MANUAL-IN',
                'notes' => $request->notes ?? 'Penambahan stok manual'
            ]);

            // 2. Update Product Stock (and optionally purchase price if provided)
            $updateData = ['stock' => $stockAfter];
            if ($request->filled('purchase_price')) {
                $updateData['purchase_price'] = $request->purchase_price;
            }
            $product->update($updateData);

            DB::commit();

            return redirect()->route('products.index')->with('success', 'Stok barang ' . $product->name . ' berhasil ditambah.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
