<x-app-layout>
    <x-slot name="title">Laporan Produk Terlaris</x-slot>
    <x-slot name="pageTitle">Produk Terlaris</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Produk Terlaris</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Top 20 Produk Terlaris</h2>
            <p class="page-header-subtitle">Daftar barang dengan volume penjualan tertinggi.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('reports.best-selling') }}" method="GET" class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-600 mb-0">Periode:</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}" required>
                    <span>sampai</span>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter me-1"></i> Terapkan</button>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">Rank</th>
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="text-center">Jml Terjual</th>
                            <th scope="col" class="text-end">Total Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($summary as $index => $item)
                            <tr>
                                <td class="text-center">
                                    @if($index == 0) <span class="badge bg-warning text-dark fs-6">🥇 1</span>
                                    @elseif($index == 1) <span class="badge bg-secondary fs-6">🥈 2</span>
                                    @elseif($index == 2) <span class="badge" style="background-color: #cd7f32; font-size: 1rem;">🥉 3</span>
                                    @else <span class="fw-bold text-muted">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark font-monospace border">{{ $item['product_code'] }}</span></td>
                                <td>{{ $item['category'] }}</td>
                                <td class="fw-600">{{ $item['product_name'] }}</td>
                                <td class="text-center"><span class="badge bg-primary fs-6">{{ $item['qty'] }}</span></td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($item['revenue'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-cart-x empty-state-icon"></i>
                                        <p class="empty-state-title">Tidak ada data penjualan</p>
                                        <p class="empty-state-text">Tidak ada produk yang terjual pada rentang tanggal yang dipilih.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
