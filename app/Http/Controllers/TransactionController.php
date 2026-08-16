<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Tampilkan daftar transaksi.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('user');

        // Filter pencarian (No Invoice / Nama Pelanggan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%');
            });
        }

        // Filter rentang tanggal
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->dateRange($request->date_from, $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Tampilkan detail transaksi.
     */
    public function show(Transaction $transaction)
    {
        // Load relasi items dan product
        $transaction->load('items.product', 'user');
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Cetak struk transaksi.
     */
    public function print(Transaction $transaction)
    {
        $transaction->load('items.product', 'user');
        
        // Return view print (hanya HTML polos untuk di-print)
        return view('transactions.print', compact('transaction'));
    }
}
