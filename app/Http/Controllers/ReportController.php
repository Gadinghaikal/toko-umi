<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReportController extends Controller
{
    /**
     * Laporan Harian: Daftar transaksi pada tanggal tertentu.
     */
    public function daily(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        $transactions = Transaction::whereDate('created_at', $date)
            ->with(['user', 'items.product'])
            ->get();
            
        $totalRevenue = $transactions->sum('grand_total');
        
        // Kalkulasi profit: total_amount - sum(price_buy * qty)
        $totalProfit = 0;
        foreach ($transactions as $trx) {
            foreach ($trx->items as $item) {
                $totalProfit += $item->total_profit;
            }
        }

        return view('reports.daily', compact('date', 'transactions', 'totalRevenue', 'totalProfit'));
    }

    /**
     * Laporan Bulanan: Rekap penjualan per hari dalam 1 bulan.
     */
    public function monthly(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        // Ambil semua transaksi di bulan dan tahun yang dipilih
        $transactions = Transaction::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->with('items.product')
            ->get();

        // Kelompokkan berdasarkan tanggal
        $summary = [];
        $totalRevenue = 0;
        $totalProfit = 0;

        foreach ($transactions as $trx) {
            $dateString = $trx->created_at->format('Y-m-d');
            
            if (!isset($summary[$dateString])) {
                $summary[$dateString] = [
                    'date' => $dateString,
                    'count' => 0,
                    'revenue' => 0,
                    'profit' => 0
                ];
            }

            $summary[$dateString]['count'] += 1;
            $summary[$dateString]['revenue'] += $trx->grand_total;
            
            $trxProfit = 0;
            foreach ($trx->items as $item) {
                $trxProfit += $item->total_profit;
            }
            $summary[$dateString]['profit'] += $trxProfit;
            
            $totalRevenue += $trx->grand_total;
            $totalProfit += $trxProfit;
        }

        // Urutkan berdasarkan tanggal
        ksort($summary);
        
        $years = Transaction::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [Carbon::now()->format('Y')];
        }

        return view('reports.monthly', compact('month', 'year', 'summary', 'totalRevenue', 'totalProfit', 'years'));
    }

    /**
     * Laporan Tahunan: Rekap penjualan per bulan dalam 1 tahun.
     */
    public function yearly(Request $request)
    {
        $year = $request->input('year', Carbon::now()->format('Y'));

        $transactions = Transaction::whereYear('created_at', $year)
            ->with('items.product')
            ->get();

        $summary = [];
        $totalRevenue = 0;
        $totalProfit = 0;

        // Inisialisasi 12 bulan
        for ($m = 1; $m <= 12; $m++) {
            $monthKey = str_pad($m, 2, '0', STR_PAD_LEFT);
            $summary[$monthKey] = [
                'month' => $monthKey,
                'count' => 0,
                'revenue' => 0,
                'profit' => 0
            ];
        }

        foreach ($transactions as $trx) {
            $monthString = $trx->created_at->format('m');
            
            $summary[$monthString]['count'] += 1;
            $summary[$monthString]['revenue'] += $trx->grand_total;
            
            $trxProfit = 0;
            foreach ($trx->items as $item) {
                $trxProfit += $item->total_profit;
            }
            $summary[$monthString]['profit'] += $trxProfit;
            
            $totalRevenue += $trx->grand_total;
            $totalProfit += $trxProfit;
        }

        $years = Transaction::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (empty($years)) {
            $years = [Carbon::now()->format('Y')];
        }

        return view('reports.yearly', compact('year', 'summary', 'totalRevenue', 'totalProfit', 'years'));
    }

    /**
     * Produk Terlaris berdasarkan range tanggal.
     */
    public function bestSelling(Request $request)
    {
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::today()->format('Y-m-d'));

        $items = TransactionItem::whereHas('transaction', function($q) use ($dateFrom, $dateTo) {
                $q->whereDate('created_at', '>=', $dateFrom)
                  ->whereDate('created_at', '<=', $dateTo);
            })
            ->with('product')
            ->get();

        $summary = [];
        
        foreach ($items as $item) {
            if (!$item->product) continue;
            
            $pid = $item->product_id;
            if (!isset($summary[$pid])) {
                $summary[$pid] = [
                    'product_code' => $item->product->code,
                    'product_name' => $item->product->name,
                    'category' => $item->product->category->name ?? '-',
                    'qty' => 0,
                    'revenue' => 0
                ];
            }
            
            $summary[$pid]['qty'] += $item->quantity;
            $summary[$pid]['revenue'] += $item->subtotal;
        }

        // Urutkan berdasarkan qty terjual terbanyak
        usort($summary, function($a, $b) {
            return $b['qty'] <=> $a['qty'];
        });

        // Ambil top 20
        $summary = array_slice($summary, 0, 20);

        return view('reports.best_selling', compact('dateFrom', 'dateTo', 'summary'));
    }

    /**
     * Laporan Stok Barang saat ini.
     */
    public function stock()
    {
        $products = Product::with('category')->get();
        
        $totalAssetValue = 0;
        foreach ($products as $p) {
            $totalAssetValue += ($p->stock * $p->purchase_price);
        }

        return view('reports.stock', compact('products', 'totalAssetValue'));
    }

    // =========================================================================
    // EXPORT PDF
    // =========================================================================
    
    public function exportPdf(Request $request, $type)
    {
        $storeName = Setting::get('store_name', 'TOKO UMI');
        $storeAddress = Setting::get('store_address', '');
        
        $data = [
            'store_name' => $storeName,
            'store_address' => $storeAddress,
            'date_generated' => Carbon::now()->format('d/m/Y H:i'),
        ];
        
        $view = '';
        $filename = '';

        if ($type === 'daily') {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $transactions = Transaction::whereDate('created_at', $date)->with('items.product')->get();
            $totalRevenue = $transactions->sum('grand_total');
            $totalProfit = 0;
            foreach ($transactions as $trx) {
                foreach ($trx->items as $item) {
                    $totalProfit += $item->total_profit;
                }
            }
            $data = array_merge($data, compact('date', 'transactions', 'totalRevenue', 'totalProfit'));
            $view = 'reports.pdf.daily';
            $filename = "Laporan_Harian_{$date}.pdf";
        } 
        elseif ($type === 'stock') {
            $products = Product::with('category')->get();
            $totalAssetValue = 0;
            foreach ($products as $p) {
                $totalAssetValue += ($p->stock * $p->purchase_price);
            }
            $data = array_merge($data, compact('products', 'totalAssetValue'));
            $view = 'reports.pdf.stock';
            $filename = "Laporan_Stok_" . date('Ymd') . ".pdf";
        } else {
            abort(404, 'Tipe ekspor PDF tidak didukung');
        }

        // Setting options DOMPDF
        $pdf = Pdf::loadView($view, $data)->setPaper('A4', 'portrait');
        return $pdf->download($filename);
    }

    // =========================================================================
    // EXPORT EXCEL (PhpSpreadsheet)
    // =========================================================================

    public function exportExcel(Request $request, $type)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $storeName = Setting::get('store_name', 'TOKO UMI');

        // Style helper
        $styleHeader = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0D6EFD']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        $styleBorder = [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ];

        if ($type === 'daily') {
            $date = $request->input('date', Carbon::today()->format('Y-m-d'));
            $transactions = Transaction::whereDate('created_at', $date)->with('items.product')->get();
            
            $sheet->setCellValue('A1', "Laporan Penjualan Harian - $storeName");
            $sheet->setCellValue('A2', "Tanggal: " . Carbon::parse($date)->format('d/m/Y'));
            
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Headers
            $headers = ['No', 'No. Invoice', 'Waktu', 'Pelanggan', 'Kasir', 'Metode Bayar', 'Total (Rp)'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $col++;
            }
            $sheet->getStyle('A4:G4')->applyFromArray($styleHeader);

            $row = 5;
            $no = 1;
            $total = 0;
            foreach ($transactions as $trx) {
                $sheet->setCellValue('A'.$row, $no++);
                $sheet->setCellValue('B'.$row, $trx->invoice_number);
                $sheet->setCellValue('C'.$row, $trx->created_at->format('H:i'));
                $sheet->setCellValue('D'.$row, $trx->notes);
                $sheet->setCellValue('E'.$row, $trx->user->name ?? '-');
                $sheet->setCellValue('F'.$row, strtoupper($trx->payment_method));
                $sheet->setCellValue('G'.$row, $trx->grand_total);
                
                $total += $trx->grand_total;
                $row++;
            }
            
            $sheet->getStyle("A4:G".($row-1))->applyFromArray($styleBorder);
            $sheet->setCellValue('F'.$row, 'TOTAL PENJUALAN');
            $sheet->setCellValue('G'.$row, $total);
            $sheet->getStyle("F{$row}:G{$row}")->getFont()->setBold(true);

            // Autofit
            foreach(range('A','G') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $filename = "Laporan_Harian_{$date}.xlsx";
        } 
        elseif ($type === 'stock') {
            $products = Product::with('category')->get();
            
            $sheet->setCellValue('A1', "Laporan Stok Barang - $storeName");
            $sheet->setCellValue('A2', "Tanggal Cetak: " . date('d/m/Y H:i'));
            
            $sheet->mergeCells('A1:G1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Headers
            $headers = ['No', 'Kode Barang', 'Kategori', 'Nama Barang', 'Harga Beli (Rp)', 'Harga Jual (Rp)', 'Stok'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $col++;
            }
            $sheet->getStyle('A4:G4')->applyFromArray($styleHeader);

            $row = 5;
            $no = 1;
            foreach ($products as $product) {
                $sheet->setCellValue('A'.$row, $no++);
                $sheet->setCellValue('B'.$row, $product->code);
                $sheet->setCellValue('C'.$row, $product->category->name ?? '-');
                $sheet->setCellValue('D'.$row, $product->name);
                $sheet->setCellValue('E'.$row, $product->price_buy);
                $sheet->setCellValue('F'.$row, $product->price_sell);
                $sheet->setCellValue('G'.$row, $product->stock);
                $row++;
            }
            
            $sheet->getStyle("A4:G".($row-1))->applyFromArray($styleBorder);

            foreach(range('A','G') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $filename = "Laporan_Stok_" . date('Ymd') . ".xlsx";
        }
        else {
            abort(404, 'Tipe ekspor Excel tidak didukung');
        }

        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'"');
        header('Cache-Control: max-age=0');
        
        // Simpan output ke stream
        $writer->save('php://output');
        exit;
    }
}
