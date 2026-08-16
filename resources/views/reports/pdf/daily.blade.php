<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Harian</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .store-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .store-address { font-size: 12px; color: #555; }
        .report-title { font-size: 16px; font-weight: bold; margin: 15px 0; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .summary { width: 50%; float: right; }
        .summary table { width: 100%; border: none; }
        .summary td { border: none; padding: 5px; }
        .summary .label { font-weight: bold; }
        .summary .value { text-align: right; font-weight: bold; font-size: 14px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="store-name">{{ $store_name }}</div>
        <div class="store-address">{{ $store_address }}</div>
    </div>

    <div class="report-title">
        LAPORAN PENJUALAN HARIAN<br>
        <span style="font-size: 12px; font-weight: normal;">Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">No. Invoice</th>
                <th style="width: 10%;">Waktu</th>
                <th style="width: 25%;">Pelanggan</th>
                <th style="width: 20%;">Kasir</th>
                <th class="text-right" style="width: 25%;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $index => $trx)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $trx->invoice_number }}</td>
                <td>{{ $trx->created_at->format('H:i') }}</td>
                <td>{{ $trx->notes }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($trx->grand_total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada transaksi pada tanggal ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="label">Total Transaksi</td>
                <td class="value">{{ $transactions->count() }}</td>
            </tr>
            <tr>
                <td class="label">Total Pendapatan</td>
                <td class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
            @if(\App\Models\Setting::get('report_show_profit', '1') == '1')
            <tr>
                <td class="label">Total Keuntungan (Profit)</td>
                <td class="value">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        Dicetak pada: {{ $date_generated }} | Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>
