<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .store-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .store-address { font-size: 12px; color: #555; }
        .report-title { font-size: 16px; font-weight: bold; margin: 15px 0; text-align: center; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; }
        th { background-color: #f4f4f4; text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
        
        .highlight { background-color: #e6f2ff; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="store-name">{{ $store_name }}</div>
        <div class="store-address">{{ $store_address }}</div>
    </div>

    <div class="report-title">
        LAPORAN STOK BARANG & VALUASI ASET<br>
        <span style="font-size: 12px; font-weight: normal;">Posisi Per Tanggal: {{ \Carbon\Carbon::now()->format('d F Y') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 5%;">No</th>
                <th style="width: 15%;">Kode Barang</th>
                <th style="width: 30%;">Nama Barang</th>
                <th class="text-right" style="width: 15%;">Harga Beli (Rp)</th>
                <th class="text-center" style="width: 10%;">Stok</th>
                <th class="text-right" style="width: 25%;">Valuasi Aset (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $product->code }}</td>
                <td>{{ $product->name }}</td>
                <td class="text-right">{{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                <td class="text-center fw-bold">{{ $product->stock }}</td>
                <td class="text-right">{{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data barang.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="highlight">
                <td colspan="5" class="text-right">TOTAL VALUASI ASET (MODAL)</td>
                <td class="text-right">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ $date_generated }} | Oleh: {{ auth()->user()->name }}
    </div>
</body>
</html>
