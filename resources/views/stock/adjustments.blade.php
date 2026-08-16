<x-app-layout>
    <x-slot name="title">Penyesuaian Stok</x-slot>
    <x-slot name="pageTitle">Penyesuaian Stok</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Inventaris</li>
                    <li class="breadcrumb-item active">Penyesuaian Stok</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Penyesuaian Stok</h2>
            <p class="page-header-subtitle">Koreksi stok barang karena rusak, kadaluarsa, atau stock opname.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.history') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> Riwayat Stok
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adjustmentModal">
                <i class="bi bi-plus-circle me-1"></i> Buat Penyesuaian
            </button>
        </div>
    </div>

    <!-- Modal Penyesuaian Stok -->
    <div class="modal fade" id="adjustmentModal" tabindex="-1" aria-labelledby="adjustmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="adjustmentModalLabel">Penyesuaian Stok Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stock.adjustments.store') }}" method="POST" data-loading>
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select class="form-select select2-products" id="product_id" name="product_id" required>
                                <option value="" disabled selected>-- Cari & Pilih Barang --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-stock="{{ $product->stock }}" {{ (isset($selectedProductId) && $selectedProductId == $product->id) ? 'selected' : '' }}>
                                        {{ $product->code }} - {{ $product->name }} (Stok: {{ $product->stock }} {{ $product->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label text-muted">Stok Saat Ini</label>
                                <input type="text" class="form-control bg-light" id="current_stock_display" readonly value="0">
                            </div>
                            <div class="col-6">
                                <label for="stock_after" class="form-label">Stok Fisik (Baru) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="stock_after" name="stock_after" required min="0">
                            </div>
                        </div>
                        
                        <div class="mb-3 p-2 bg-light rounded text-center" id="difference_alert" style="display: none;">
                            Selisih: <span id="difference_value" class="fw-bold">0</span>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Alasan Penyesuaian <span class="text-danger">*</span></label>
                            <select class="form-select" id="reason" name="reason" required>
                                <option value="" disabled selected>-- Pilih Alasan --</option>
                                @foreach($reasons as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Detail penyesuaian..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitAdjustment" disabled>Simpan Penyesuaian</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('stock.adjustments') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-auto fw-600">Filter:</div>
                <div class="col-6 col-md-3">
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}" placeholder="Dari">
                </div>
                <div class="col-6 col-md-3">
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}" placeholder="Sampai">
                </div>
                <div class="col-12 col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Cari barang atau kode ADJ..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        @if(request('date_from') || request('date_to') || request('search'))
                            <a href="{{ route('stock.adjustments') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                        @endif
                    </div>
                </div>
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
                            <th scope="col" class="text-center">Tanggal</th>
                            <th scope="col">Kode ADJ</th>
                            <th scope="col">Barang</th>
                            <th scope="col" class="text-center">S. Awal</th>
                            <th scope="col" class="text-center">S. Akhir</th>
                            <th scope="col" class="text-center">Selisih</th>
                            <th scope="col">Alasan</th>
                            <th scope="col">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($adjustments as $adj)
                            <tr>
                                <td class="text-center">{{ $adj->created_at->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $adj->adjustment_code }}</span></td>
                                <td>
                                    <div class="fw-600">{{ $adj->product->name ?? '-' }}</div>
                                </td>
                                <td class="text-center text-muted">{{ $adj->stock_before }}</td>
                                <td class="text-center fw-bold">{{ $adj->stock_after }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $adj->difference > 0 ? 'success' : ($adj->difference < 0 ? 'danger' : 'secondary') }}">
                                        {{ $adj->formatted_difference }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-600" style="font-size: 0.85rem;">{{ $adj->reason_label }}</div>
                                    @if($adj->notes)
                                        <div class="text-muted small text-truncate" style="max-width: 150px;" title="{{ $adj->notes }}">{{ $adj->notes }}</div>
                                    @endif
                                </td>
                                <td>{{ $adj->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-sliders empty-state-icon"></i>
                                        <p class="empty-state-title">Belum ada penyesuaian stok</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($adjustments->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $adjustments->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productSelect = document.getElementById('product_id');
            const currentStockDisplay = document.getElementById('current_stock_display');
            const stockAfterInput = document.getElementById('stock_after');
            const differenceAlert = document.getElementById('difference_alert');
            const differenceValue = document.getElementById('difference_value');
            const btnSubmit = document.getElementById('btnSubmitAdjustment');

            // Open modal automatically if selectedProductId is present in URL
            @if(isset($selectedProductId) && $selectedProductId)
                const adjModal = new bootstrap.Modal(document.getElementById('adjustmentModal'));
                adjModal.show();
                updateStockInfo();
            @endif

            function updateStockInfo() {
                if (productSelect.selectedIndex > 0) {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
                    currentStockDisplay.value = stock;
                    
                    if (!stockAfterInput.value) {
                        stockAfterInput.value = stock;
                    }
                    
                    calculateDifference();
                } else {
                    currentStockDisplay.value = '0';
                    stockAfterInput.value = '';
                    differenceAlert.style.display = 'none';
                    btnSubmit.disabled = true;
                }
            }

            function calculateDifference() {
                const currentStock = parseInt(currentStockDisplay.value) || 0;
                const newStock = parseInt(stockAfterInput.value);
                
                if (isNaN(newStock) || newStock < 0 || productSelect.selectedIndex <= 0) {
                    differenceAlert.style.display = 'none';
                    btnSubmit.disabled = true;
                    return;
                }
                
                const diff = newStock - currentStock;
                differenceAlert.style.display = 'block';
                
                if (diff > 0) {
                    differenceValue.textContent = '+' + diff;
                    differenceValue.className = 'fw-bold text-success';
                    differenceAlert.className = 'mb-3 p-2 bg-success-soft rounded text-center';
                    btnSubmit.disabled = false;
                } else if (diff < 0) {
                    differenceValue.textContent = diff;
                    differenceValue.className = 'fw-bold text-danger';
                    differenceAlert.className = 'mb-3 p-2 bg-danger-soft rounded text-center';
                    btnSubmit.disabled = false;
                } else {
                    differenceValue.textContent = '0 (Tidak ada perubahan)';
                    differenceValue.className = 'fw-bold text-muted';
                    differenceAlert.className = 'mb-3 p-2 bg-light rounded text-center';
                    btnSubmit.disabled = true; // Disable if no changes
                }
            }

            productSelect.addEventListener('change', updateStockInfo);
            stockAfterInput.addEventListener('input', calculateDifference);
        });
    </script>
    @endpush
</x-app-layout>
