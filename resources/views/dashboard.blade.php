<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>

    {{-- ================================================================
         PAGE HEADER
         ================================================================ --}}
    <div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-2">
        <div>
            <h2 class="page-header-title">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
            <p class="page-header-subtitle">
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} &mdash;
                Ringkasan operasional toko hari ini
            </p>
        </div>
        <a href="{{ route('kasir.index') }}" class="btn btn-action text-white fw-600 px-4 shadow-sm" style="border-radius: var(--radius-base);">
            <i class="bi bi-cart-plus me-1"></i> Buka Kasir
        </a>
    </div>

    {{-- ================================================================
         STAT CARDS (4 kartu utama)
         ================================================================ --}}
    <div class="row g-3 mb-4">

        {{-- 1. Pendapatan Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-primary h-100">
                <div class="stat-card-top">
                    <p class="stat-card-label">Pendapatan Hari Ini</p>
                    <div class="stat-card-icon bg-primary-soft">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <p class="stat-card-value">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </p>
                <div class="stat-card-footer">
                    <span class="stat-badge {{ $revenueGrowth >= 0 ? 'up' : 'down' }}">
                        <i class="bi bi-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i>
                        {{ abs($revenueGrowth) }}%
                    </span>
                    <span>vs bulan lalu</span>
                </div>
            </div>
        </div>

        {{-- 2. Transaksi Hari Ini --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-success h-100">
                <div class="stat-card-top">
                    <p class="stat-card-label">Transaksi Hari Ini</p>
                    <div class="stat-card-icon bg-success-soft">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
                <p class="stat-card-value">{{ number_format($todayTransactionCount) }}</p>
                <div class="stat-card-footer">
                    <i class="bi bi-clock text-muted"></i>
                    <span>Transaksi selesai</span>
                </div>
            </div>
        </div>

        {{-- 3. Pendapatan Bulan Ini --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card stat-info h-100">
                <div class="stat-card-top">
                    <p class="stat-card-label">Pendapatan Bulan Ini</p>
                    <div class="stat-card-icon bg-info-soft">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
                <p class="stat-card-value">
                    Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                </p>
                <div class="stat-card-footer">
                    <i class="bi bi-calendar-month text-muted"></i>
                    <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('MMMM YYYY') }}</span>
                </div>
            </div>
        </div>

        {{-- 4. Total Produk / Alert Stok --}}
        <div class="col-6 col-lg-3">
            <div class="stat-card {{ $lowStockCount > 0 || $outOfStockCount > 0 ? 'stat-warning' : 'stat-secondary' }} h-100">
                <div class="stat-card-top">
                    <p class="stat-card-label">Total Produk</p>
                    <div class="stat-card-icon {{ $lowStockCount > 0 || $outOfStockCount > 0 ? 'bg-warning-soft' : 'bg-secondary-soft' }}">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
                <p class="stat-card-value">{{ number_format($totalProducts) }}</p>
                <div class="stat-card-footer">
                    @if($outOfStockCount > 0)
                        <span class="stat-badge down">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            {{ $outOfStockCount }} habis
                        </span>
                    @endif
                    @if($lowStockCount > 0)
                        <span class="stat-badge" style="background:#FFFBEB;color:#D97706;">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            {{ $lowStockCount }} menipis
                        </span>
                    @endif
                    @if($outOfStockCount == 0 && $lowStockCount == 0)
                        <span class="stat-badge up">
                            <i class="bi bi-check-circle-fill"></i> Semua aman
                        </span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ================================================================
         ROW 2: Grafik + Peringatan Stok
         ================================================================ --}}
    <div class="row g-3 mb-4">

        {{-- Grafik 7 Hari --}}
        <div class="col-12 col-lg-7">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-bar-chart-line me-2 text-primary"></i>Pendapatan 7 Hari Terakhir</span>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="230"></canvas>
                </div>
            </div>
        </div>

        {{-- Peringatan Stok Menipis --}}
        <div class="col-12 col-lg-5">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span>
                        <i class="bi bi-exclamation-triangle me-2 text-warning"></i>Peringatan Stok
                    </span>
                    @if($lowStockCount > 0 || $outOfStockCount > 0)
                        <a href="{{ route('products.index') }}?filter=low_stock"
                           class="btn btn-sm btn-outline-secondary py-0 px-2"
                           style="font-size:0.72rem;">Lihat Semua</a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($lowStockProducts->isEmpty() && $outOfStockCount == 0)
                        <div class="empty-state py-4">
                            <i class="bi bi-check-circle-fill empty-state-icon text-success"></i>
                            <p class="empty-state-title">Semua stok aman!</p>
                            <p class="empty-state-text">Tidak ada produk dengan stok menipis atau habis.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($lowStockProducts as $product)
                                <div class="list-group-item list-group-item-action px-3 py-2 border-0 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="flex-shrink-0">
                                            @if($product->stock <= 0)
                                                <span class="badge bg-danger">Habis</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Menipis</span>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <p class="mb-0 fw-600 text-truncate" style="font-size:0.8rem;">
                                                {{ $product->name }}
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size:0.72rem;">
                                                {{ $product->category->name ?? '-' }}
                                            </p>
                                        </div>
                                        <div class="text-end flex-shrink-0">
                                            <span class="fw-700 {{ $product->stock <= 0 ? 'text-danger' : 'text-warning' }}"
                                                  style="font-size:0.85rem;">
                                                {{ $product->stock }}
                                            </span>
                                            <span class="text-muted" style="font-size:0.72rem;">
                                                /{{ $product->min_stock }} {{ $product->unit }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($outOfStockCount > 0)
                            <div class="px-3 py-2 bg-danger bg-opacity-10 border-top">
                                <p class="mb-0 text-danger fw-600" style="font-size:0.78rem;">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                    {{ $outOfStockCount }} produk stok habis
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ================================================================
         ROW 3: Transaksi Terbaru + Produk Terlaris
         ================================================================ --}}
    <div class="row g-3">

        {{-- Transaksi Terbaru --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-receipt me-2 text-primary"></i>Transaksi Terbaru</span>
                    <a href="{{ route('transactions.index') }}"
                       class="btn btn-sm btn-outline-secondary py-0 px-2"
                       style="font-size:0.72rem;">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    @if($recentTransactions->isEmpty())
                        <div class="empty-state py-4">
                            <i class="bi bi-receipt empty-state-icon"></i>
                            <p class="empty-state-title">Belum ada transaksi hari ini</p>
                            <p class="empty-state-text">Transaksi yang selesai akan muncul di sini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Waktu</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $trx)
                                        <tr>
                                            <td>
                                                <a href="{{ route('transactions.show', $trx->id) }}"
                                                   class="fw-600 text-decoration-none text-primary"
                                                   style="font-size:0.8rem;">
                                                    {{ $trx->invoice_number }}
                                                </a>
                                            </td>
                                            <td class="text-muted" style="font-size:0.78rem;">
                                                {{ $trx->transaction_date ? $trx->transaction_date->format('H:i') : '-' }}
                                            </td>
                                            <td style="font-size:0.78rem;">{{ $trx->user->name ?? '-' }}</td>
                                            <td class="text-end fw-600" style="font-size:0.82rem;">
                                                Rp {{ number_format($trx->grand_total, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Produk Terlaris Hari Ini --}}
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-trophy me-2 text-warning"></i>Terlaris Hari Ini</span>
                    <a href="{{ route('reports.best-selling') }}"
                       class="btn btn-sm btn-outline-secondary py-0 px-2"
                       style="font-size:0.72rem;">Laporan</a>
                </div>
                <div class="card-body p-0">
                    @if($topProductsToday->isEmpty())
                        <div class="empty-state py-4">
                            <i class="bi bi-trophy empty-state-icon"></i>
                            <p class="empty-state-title">Belum ada penjualan</p>
                            <p class="empty-state-text">Data produk terlaris akan muncul setelah ada transaksi.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($topProductsToday as $idx => $product)
                                <div class="list-group-item border-0 border-bottom px-3 py-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0 fw-800 text-muted"
                                             style="width:20px;font-size:0.75rem;text-align:center;">
                                            #{{ $idx + 1 }}
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <p class="mb-0 fw-600 text-truncate" style="font-size:0.8rem;">
                                                {{ $product->product_name }}
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size:0.72rem;">
                                                Rp {{ number_format($product->total_revenue, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-primary-soft text-primary fw-700"
                                                  style="font-size:0.72rem;">
                                                {{ $product->total_qty }} terjual
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // ================================================================
    // Grafik Pendapatan 7 Hari (Chart.js)
    // ================================================================
    (function () {
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;

        const labels  = @json($last7Days->pluck('date'));
        const data    = @json($last7Days->pluck('revenue'));

        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 230);
        gradient.addColorStop(0, 'rgba(37,99,235,0.25)');
        gradient.addColorStop(1, 'rgba(37,99,235,0.02)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: '#2563EB',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(37,99,235,0.45)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' + Number(context.raw).toLocaleString('id-ID');
                            }
                        },
                        backgroundColor: '#0F172A',
                        titleColor: '#94A3B8',
                        bodyColor: '#FFFFFF',
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#94A3B8' },
                        border: { display: false }
                    },
                    y: {
                        grid: { color: '#F1F5F9', drawBorder: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#94A3B8',
                            callback: function (value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });
    })();
    </script>
    @endpush

</x-app-layout>
