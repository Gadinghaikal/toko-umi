<x-app-layout>
    <x-slot name="title">Kategori Produk</x-slot>
    <x-slot name="pageTitle">Kategori Produk</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Master Data</li>
                    <li class="breadcrumb-item active">Kategori</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Kategori Produk</h2>
            <p class="page-header-subtitle">Kelola kategori untuk mengelompokkan produk Anda</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Kategori
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Daftar Kategori</h5>
            <form action="{{ route('categories.index') }}" method="GET" class="d-flex align-items-center" style="max-width: 300px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari kategori..." value="{{ request('search') }}">
                    @if(request('search'))
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary border-start-0"><i class="bi bi-x-circle"></i></a>
                    @endif
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Nama Kategori</th>
                            <th scope="col">Keterangan</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td class="text-center">{{ $categories->firstItem() + $index }}</td>
                                <td class="fw-600">{{ $category->name }}</td>
                                <td>{{ $category->description ?: '-' }}</td>
                                <td class="text-center">
                                    @if ($category->is_active)
                                        <span class="badge bg-success-soft text-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" data-loading>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Hapus" data-confirm-delete="Apakah Anda yakin ingin menghapus kategori '{{ $category->name }}'?">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-tags empty-state-icon"></i>
                                        <p class="empty-state-title">Belum ada kategori</p>
                                        <p class="empty-state-text">Silakan tambah kategori baru terlebih dahulu.</p>
                                        <a href="{{ route('categories.create') }}" class="btn btn-primary mt-2">Tambah Kategori Pertama</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $categories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</x-app-layout>
