<x-app-layout>
    <x-slot name="title">Laporan Penjualan Bulanan</x-slot>
    <x-slot name="pageTitle">Laporan Bulanan</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Laporan</li>
                    <li class="breadcrumb-item active">Bulanan</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Laporan Penjualan Bulanan</h2>
            <p class="page-header-subtitle">Rekapitulasi penjualan per hari dalam satu bulan.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="{{ route('reports.monthly') }}" method="GET" class="d-flex align-items-center gap-3">
                <label class="fw-600 mb-0">Pilih Bulan & Tahun:</label>
                <select name="month" class="form-select" style="width: 150px;">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                        </option>
                    @endfor
                </select>
                <select name="year" class="form-select" style="width: 120px;">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Tampilkan</button>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="card border-start border-4 border-success h-100">
                <div class="card-body">
                    <h6 class="text-muted fw-bold text-uppercase mb-2">Total Pendapatan Bulan Ini</h6>
                    <h2 class="mb-0 fw-bold text-success">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        @if(\App\Models\Setting::get('report_show_profit', '1') == '1')
        <div class="col-12 col-md-6">
            <div class="card border-start border-4 border-info h-100">
                <div class="card-body">
                    <h6 class="text-muted fw-bold text-uppercase mb-2">Total Profit Bulan Ini</h6>
                    <h2 class="mb-0 fw-bold text-info">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h2>
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
                            <th scope="col">Tanggal</th>
                            <th scope="col" class="text-center">Jml Transaksi</th>
                            <th scope="col" class="text-end">Total Penjualan</th>
                            @if(\App\Models\Setting::get('report_show_profit', '1') == '1')
                            <th scope="col" class="text-end">Keuntungan (Profit)</th>
                            @endif
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 1; @endphp
                        @forelse ($summary as $date => $data)
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-bold">{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</td>
                                <td class="text-center">{{ $data['count'] }} Transaksi</td>
                                <td class="text-end text-success fw-bold">Rp {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                                @if(\App\Models\Setting::get('report_show_profit', '1') == '1')
                                <td class="text-end text-info fw-bold">Rp {{ number_format($data['profit'], 0, ',', '.') }}</td>
                                @endif
                                <td class="text-center">
                                    <a href="{{ route('reports.daily', ['date' => $date]) }}" class="btn btn-sm btn-outline-primary">Lihat Rincian</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-calendar-x empty-state-icon"></i>
                                        <p class="empty-state-title">Tidak ada data</p>
                                        <p class="empty-state-text">Belum ada transaksi pada bulan {{ date('F', mktime(0,0,0,$month,10)) }} {{ $year }}.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($summary) > 0)
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL KESELURUHAN</td>
                            <td class="text-end fw-bold fs-6">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            @if(\App\Models\Setting::get('report_show_profit', '1') == '1')
                            <td class="text-end fw-bold fs-6">Rp {{ number_format($totalProfit, 0, ',', '.') }}</td>
                            @endif
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
