<x-app-layout>
    <x-slot name="title">Laporan Stok Barang</x-slot>
    <x-slot name="pageTitle">Laporan Stok</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Stok</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Laporan Posisi Stok Barang</h2>
            <p class="page-header-subtitle">Daftar sisa stok saat ini beserta valuasi aset.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.export.pdf', 'stock') }}" target="_blank" class="btn btn-outline-danger">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>
            <a href="{{ route('reports.export.excel', 'stock') }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Summary Asset -->
    <div class="card mb-4 border-start border-4 border-primary">
        <div class="card-body">
            <h6 class="text-muted fw-bold text-uppercase mb-2">Total Valuasi Aset (Modal Barang)</h6>
            <h2 class="mb-0 fw-bold text-primary">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h2>
            <p class="text-muted small mt-1 mb-0">*Dihitung dari sisa stok dikalikan harga beli masing-masing barang.</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Kode Barang</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col" class="text-end">Harga Beli</th>
                            <th scope="col" class="text-center">Sisa Stok</th>
                            <th scope="col" class="text-end">Subtotal Valuasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $product)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><span class="badge bg-light text-dark font-monospace border">{{ $product->code }}</span></td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td class="fw-600">{{ $product->name }}</td>
                                <td class="text-end">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $product->stock <= 5 ? 'bg-danger' : 'bg-primary' }} fs-6">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($product->stock * $product->purchase_price, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-box empty-state-icon"></i>
                                        <p class="empty-state-title">Tidak ada barang</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($products->isNotEmpty())
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold">TOTAL VALUASI ASET</td>
                            <td class="text-end fw-bold fs-6 text-primary">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
