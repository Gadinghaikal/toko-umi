<x-app-layout>
    <x-slot name="title">Data Barang (Produk)</x-slot>
    <x-slot name="pageTitle">Data Barang</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Data</li>
                    <li class="breadcrumb-item active">Barang</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Data Barang</h2>
            <p class="page-header-subtitle">Kelola informasi barang, harga, dan ketersediaan stok</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index', ['filter' => 'low_stock']) }}" class="btn btn-outline-warning">
                <i class="bi bi-exclamation-triangle me-1"></i> Stok Menipis
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Barang
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Filter & Pencarian</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('products.index') }}" method="GET" class="row g-3 align-items-end">
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1" style="font-size: 0.85rem;">Kategori</label>
                    <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label mb-1" style="font-size: 0.85rem;">Cari Nama/Kode</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-3 text-md-end">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Terapkan</button>
                    @if(request('search') || request('category_id') || request('filter'))
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-link text-decoration-none d-block mt-1">Reset Filter</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Daftar Barang</h5>
            <span class="badge bg-primary-soft text-primary rounded-pill">{{ $products->total() }} Barang</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Kode</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Kategori</th>
                            <th scope="col" class="text-end">Harga Beli</th>
                            <th scope="col" class="text-end">Harga Jual</th>
                            <th scope="col" class="text-center">Stok</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $index => $product)
                            <tr>
                                <td class="text-center">{{ $products->firstItem() + $index }}</td>
                                <td><span class="badge bg-light text-dark font-monospace border">{{ $product->code }}</span></td>
                                <td>
                                    <div class="fw-600">{{ $product->name }}</div>
                                </td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-success">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @if($product->stock <= $product->min_stock)
                                        <span class="badge bg-danger rounded-pill" data-bs-toggle="tooltip" title="Stok Minimum: {{ $product->min_stock }}">
                                            {{ $product->stock }} {{ $product->unit }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark rounded-pill">
                                            {{ $product->stock }} {{ $product->unit }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($product->is_active)
                                        <span class="badge bg-success-soft text-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline" data-loading>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Hapus" data-confirm-delete="Apakah Anda yakin ingin menghapus barang '{{ $product->name }}'?">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="bi bi-box-seam empty-state-icon"></i>
                                        <p class="empty-state-title">Belum ada barang</p>
                                        <p class="empty-state-text">Silakan tambah barang baru atau sesuaikan pencarian Anda.</p>
                                        @if(request('search') || request('category_id') || request('filter'))
                                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary mt-2">Reset Filter</a>
                                        @else
                                            <a href="{{ route('products.create') }}" class="btn btn-primary mt-2">Tambah Barang Pertama</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($products->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</x-app-layout>
