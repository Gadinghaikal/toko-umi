<x-app-layout>
    <x-slot name="title">Riwayat Stok</x-slot>
    <x-slot name="pageTitle">Riwayat Stok</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Inventaris</li>
                    <li class="breadcrumb-item active">Riwayat Stok</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Riwayat Pergerakan Stok</h2>
            <p class="page-header-subtitle">Pantau semua aktivitas stok masuk, keluar, dan penyesuaian.</p>
        </div>
        <div>
            <a href="{{ route('stock.adjustments') }}" class="btn btn-outline-primary">
                <i class="bi bi-sliders me-1"></i> Penyesuaian Stok
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom-0 pb-0">
            <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Filter Riwayat</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('stock.history') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size: 0.85rem;">Tipe</label>
                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size: 0.85rem;">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size: 0.85rem;">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size: 0.85rem;">Cari Barang</label>
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Nama / Kode" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </div>
                
                @if(request('type') || request('date_from') || request('date_to') || request('search'))
                    <div class="col-12 mt-2">
                        <a href="{{ route('stock.history') }}" class="btn btn-sm btn-link text-decoration-none p-0">Reset Filter</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center">Waktu</th>
                            <th scope="col">Barang</th>
                            <th scope="col" class="text-center">Tipe</th>
                            <th scope="col" class="text-center">Stok Awal</th>
                            <th scope="col" class="text-center">Perubahan</th>
                            <th scope="col" class="text-center">Stok Akhir</th>
                            <th scope="col">Catatan & Referensi</th>
                            <th scope="col">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histories as $history)
                            <tr>
                                <td class="text-center">
                                    <div class="fw-bold">{{ $history->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $history->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="fw-600">{{ $history->product->name ?? 'Barang Dihapus' }}</div>
                                    <small class="text-muted">{{ $history->product->code ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $history->type_badge_color }}-soft text-{{ $history->type_badge_color }}">
                                        {{ $history->type_label }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">{{ $history->quantity_before }}</td>
                                <td class="text-center fw-bold text-{{ $history->quantity_change > 0 ? 'success' : ($history->quantity_change < 0 ? 'danger' : 'secondary') }}">
                                    {{ $history->formatted_quantity_change }}
                                </td>
                                <td class="text-center fw-bold">{{ $history->quantity_after }}</td>
                                <td>
                                    <div>{{ $history->notes ?: '-' }}</div>
                                    @if($history->reference)
                                        <small class="text-muted">Ref: {{ $history->reference }}</small>
                                    @endif
                                </td>
                                <td>{{ $history->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-clock-history empty-state-icon"></i>
                                        <p class="empty-state-title">Belum ada riwayat stok</p>
                                        <p class="empty-state-text">Data pergerakan stok akan muncul di sini secara otomatis.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($histories->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $histories->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</x-app-layout>
