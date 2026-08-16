<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk - {{ $transaction->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 10px;
            width: 58mm; /* Lebar standar printer thermal kasir */
        }
        h1, h2, h3, h4, h5, p {
            margin: 0;
            padding: 0;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        
        .header { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .store-name { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
        
        .info { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .info table { width: 100%; font-size: 11px; }
        
        .items { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .items table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .items th { border-bottom: 1px dashed #000; text-align: left; padding: 2px 0; }
        .items td { padding: 2px 0; vertical-align: top; }
        
        .summary { margin-bottom: 10px; }
        .summary table { width: 100%; font-size: 11px; }
        
        .footer { text-align: center; font-size: 10px; margin-top: 15px; }

        @media print {
            body { margin: 0; padding: 0; width: 100%; }
            @page { margin: 0; size: 58mm auto; }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="header text-center">
        <div class="store-name">TOKO UMI</div>
        <div>Sembako & Pertanian</div>
    </div>

    <div class="info">
        <table>
            <tr><td>Tgl</td><td>: {{ $transaction->created_at->format('d/m/Y H:i') }}</td></tr>
            <tr><td>Inv</td><td>: {{ $transaction->invoice_number }}</td></tr>
            <tr><td>Ksr</td><td>: {{ $transaction->user->name ?? '-' }}</td></tr>
            <tr><td>Plg</td><td>: {{ $transaction->notes }}</td></tr>
        </table>
    </div>

    <div class="items">
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->items as $item)
                <tr>
                    <td colspan="3">{{ $item->product->name ?? 'Barang' }}</td>
                </tr>
                <tr>
                    <td>@ {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-center">x{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td class="fw-bold">Total</td>
                <td class="text-right fw-bold">{{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Bayar ({{ strtoupper($transaction->payment_method) }})</td>
                <td class="text-right">{{ number_format($transaction->amount_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">
                    @php
                        $kembalian = $transaction->amount_paid - $transaction->grand_total;
                    @endphp
                    {{ number_format($kembalian > 0 ? $kembalian : 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Terima Kasih</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>

    <!-- Script to automatically close window after printing (optional, commented out) -->
    <!-- <script>
        window.onafterprint = function() {
            window.close();
        };
    </script> -->
</body>
</html>
