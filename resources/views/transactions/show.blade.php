<x-app-layout>
    <x-slot name="title">Detail Transaksi {{ $transaction->invoice_number }}</x-slot>
    <x-slot name="pageTitle">Detail Transaksi</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('transactions.index') }}" class="text-decoration-none">Transaksi</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Invoice #{{ $transaction->invoice_number }}</h2>
            <p class="page-header-subtitle">Rincian lengkap untuk transaksi ini.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('transactions.print', $transaction->id) }}" target="_blank" class="btn btn-primary">
                <i class="bi bi-printer me-1"></i> Cetak Struk
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Daftar Belanja ({{ $transaction->items->sum('quantity') }} Item)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="text-center" style="width: 50px;">No</th>
                                    <th scope="col">Nama Barang</th>
                                    <th scope="col" class="text-end">Harga Satuan</th>
                                    <th scope="col" class="text-center">Qty</th>
                                    <th scope="col" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->items as $index => $item)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-600">{{ $item->product->name ?? 'Barang Dihapus' }}</div>
                                            <small class="text-muted">{{ $item->product->code ?? '-' }}</small>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Grand Total</td>
                                    <td class="text-end fw-bold text-success fs-6">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-xl-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Informasi Transaksi</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted fw-normal">Waktu</dt>
                        <dd class="col-sm-7 fw-600">{{ $transaction->created_at->format('d M Y, H:i') }}</dd>

                        <dt class="col-sm-5 text-muted fw-normal">Pelanggan</dt>
                        <dd class="col-sm-7 fw-600">{{ $transaction->notes }}</dd>

                        <dt class="col-sm-5 text-muted fw-normal">Kasir</dt>
                        <dd class="col-sm-7 fw-600">{{ $transaction->user->name ?? '-' }}</dd>

                        <hr class="my-2">

                        <dt class="col-sm-5 text-muted fw-normal">Metode Bayar</dt>
                        <dd class="col-sm-7 fw-600 text-uppercase">{{ $transaction->payment_method }}</dd>

                        <dt class="col-sm-5 text-muted fw-normal">Total Belanja</dt>
                        <dd class="col-sm-7 fw-bold fs-6">Rp {{ number_format($transaction->grand_total, 0, ',', '.') }}</dd>

                        <dt class="col-sm-5 text-muted fw-normal">Dibayar</dt>
                        <dd class="col-sm-7 fw-600">Rp {{ number_format($transaction->amount_paid, 0, ',', '.') }}</dd>

                        <dt class="col-sm-5 text-muted fw-normal">Kembalian</dt>
                        <dd class="col-sm-7 fw-600">
                            @php
                                $kembalian = $transaction->amount_paid - $transaction->grand_total;
                            @endphp
                            Rp {{ number_format($kembalian > 0 ? $kembalian : 0, 0, ',', '.') }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
