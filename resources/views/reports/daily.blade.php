<x-app-layout>
    <x-slot name="title">Laporan Penjualan Harian</x-slot>
    <x-slot name="pageTitle">Laporan Harian</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Harian</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Laporan Penjualan Harian</h2>
            <p class="page-header-subtitle">Rekapitulasi penjualan pada tanggal tertentu.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.export.pdf', 'daily') }}?date={{ $date }}" target="_blank" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('reports.export.excel', 'daily') }}?date={{ $date }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('reports.daily') }}" method="GET" class="d-flex align-items-center gap-3">
                <label for="date" class="fw-600 mb-0">Pilih Tanggal:</label>
                <input type="date" id="date" name="date" class="form-control" style="width: 200px;" value="{{ $date }}" required>
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Tampilkan</button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Transaksi</h6>
                            <h3 class="mb-0 fw-bold">{{ $transactions->count() }}</h3>
                        </div>
                        <div class="fs-1 text-white-50"><i class="bi bi-receipt"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Pendapatan</h6>
                            <h3 class="mb-0 fw-bold">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="fs-1 text-white-50"><i class="bi bi-cash-stack"></i></div>
                    </div>
                </div>
            </div>
        </div>
        @if(\App\Models\Setting::get('report_show_profit', '1') == '1')
        <div class="col-12 col-md-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Keuntungan (Profit)</h6>
                            <h3 class="mb-0 fw-bold">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                        </div>
                        <div class="fs-1 text-white-50"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Waktu</th>
                            <th scope="col">No. Invoice</th>
                            <th scope="col">Pelanggan</th>
                            <th scope="col">Kasir</th>
                            <th scope="col" class="text-end">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $index => $trx)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $trx->created_at->format('H:i') }}</td>
                                <td><a href="{{ route('transactions.show', $trx->id) }}" class="fw-bold text-decoration-none">{{ $trx->invoice_number }}</a></td>
                                <td>{{ $trx->notes }}</td>
                                <td>{{ $trx->user->name ?? '-' }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-folder-x empty-state-icon"></i>
                                        <p class="empty-state-title">Tidak ada data</p>
                                        <p class="empty-state-text">Belum ada transaksi pada tanggal {{ \Carbon\Carbon::parse($date)->format('d F Y') }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($transactions->isNotEmpty())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">TOTAL PENDAPATAN</td>
                            <td class="text-end fw-bold fs-6">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
