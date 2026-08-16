<x-app-layout>
    <x-slot name="title">Riwayat Transaksi</x-slot>
    <x-slot name="pageTitle">Riwayat Transaksi</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Transaksi</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Riwayat Penjualan</h2>
            <p class="page-header-subtitle">Daftar semua transaksi yang terjadi melalui Kasir POS.</p>
        </div>
        <a href="{{ route('kasir.index') }}" class="btn btn-primary">
            <i class="bi bi-shop me-1"></i> Buka Kasir POS
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('transactions.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-auto fw-600">Filter:</div>
                <div class="col-6 col-md-3">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="Dari">
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="Sampai">
                </div>
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="No. Invoice atau Nama Pelanggan..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request('date_from') || request('date_to') || request('search'))
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center">Tanggal</th>
                            <th scope="col">No. Invoice</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col" class="text-end">Total</th>
                            <th scope="col" class="text-center">Pembayaran</th>
                            <th scope="col">Kasir</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $trx)
                            <tr>
                                <td class="text-center">
                                    <div class="fw-bold">{{ $trx->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $trx->created_at->format('H:i') }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark font-monospace border">{{ $trx->invoice_number }}</span></td>
                                <td class="fw-600">{{ $trx->notes }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-soft text-secondary text-uppercase">{{ $trx->payment_method }}</span>
                                </td>
                                <td>{{ $trx->user->name ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('transactions.show', $trx->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Lihat Detail">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('transactions.print', $trx->id) }}" target="_blank" class="btn btn-outline-secondary" data-bs-toggle="tooltip" title="Cetak Struk">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-receipt empty-state-icon"></i>
                                        <p class="empty-state-title">Belum ada transaksi</p>
                                        <p class="empty-state-text">Silakan lakukan penjualan melalui menu Kasir POS.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</x-app-layout>
