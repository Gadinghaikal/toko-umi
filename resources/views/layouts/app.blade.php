<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDescription ?? 'TOKO UMI — Sistem Informasi Penjualan & Inventaris' }}">

    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'TOKO UMI') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>

{{-- Sidebar Backdrop (mobile overlay) --}}
<div id="sidebarBackdrop" class="sidebar-backdrop"></div>

{{-- App Wrapper --}}
<div class="app-wrapper">

    {{-- ================================================================
         SIDEBAR
         ================================================================ --}}
    <aside id="appSidebar" class="app-sidebar">

        {{-- Brand --}}
        <a href="{{ route('dashboard') }}" class="sidebar-brand">
            <div class="sidebar-brand-icon" style="background: transparent; box-shadow: none;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 28px; height: 28px; object-fit: contain;">
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">TOKO UMI</span>
                <span class="sidebar-brand-sub">Manajemen Toko</span>
            </div>
        </a>

        {{-- Nav Scroll Area --}}
        <div class="sidebar-nav-wrap">

            {{-- MENU UTAMA --}}
            <span class="sidebar-label">Menu Utama</span>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 nav-icon"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('kasir.index') }}"
                       class="nav-link {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3 nav-icon"></i>
                        <span class="nav-text">Kasir / POS</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-divider"></div>

            {{-- MASTER DATA --}}
            <span class="sidebar-label">Master Data</span>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('categories.index') }}"
                       class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags nav-icon"></i>
                        <span class="nav-text">Kategori</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('products.index') }}"
                       class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam nav-icon"></i>
                        <span class="nav-text">Produk / Barang</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-divider"></div>

            {{-- INVENTARIS --}}
            <span class="sidebar-label">Inventaris</span>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('stock.history') }}"
                       class="nav-link {{ request()->routeIs('stock.history') ? 'active' : '' }}">
                        <i class="bi bi-clock-history nav-icon"></i>
                        <span class="nav-text">Riwayat Stok</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('stock.adjustments') }}"
                       class="nav-link {{ request()->routeIs('stock.adjustments') ? 'active' : '' }}">
                        <i class="bi bi-sliders nav-icon"></i>
                        <span class="nav-text">Penyesuaian Stok</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-divider"></div>

            {{-- PENJUALAN --}}
            <span class="sidebar-label">Penjualan</span>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('transactions.index') }}"
                       class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt nav-icon"></i>
                        <span class="nav-text">Riwayat Transaksi</span>
                    </a>
                </li>
            </ul>

            @if(auth()->user()->isAdmin())
            <div class="sidebar-divider"></div>

            {{-- LAPORAN (Admin Only) --}}
            <span class="sidebar-label">Laporan</span>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('reports.daily') }}"
                       class="nav-link {{ request()->routeIs('reports.daily') ? 'active' : '' }}">
                        <i class="bi bi-calendar-day nav-icon"></i>
                        <span class="nav-text">Laporan Harian</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.monthly') }}"
                       class="nav-link {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                        <i class="bi bi-calendar-month nav-icon"></i>
                        <span class="nav-text">Laporan Bulanan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.yearly') }}"
                       class="nav-link {{ request()->routeIs('reports.yearly') ? 'active' : '' }}">
                        <i class="bi bi-calendar3 nav-icon"></i>
                        <span class="nav-text">Laporan Tahunan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.best-selling') }}"
                       class="nav-link {{ request()->routeIs('reports.best-selling') ? 'active' : '' }}">
                        <i class="bi bi-trophy nav-icon"></i>
                        <span class="nav-text">Produk Terlaris</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('reports.stock') }}"
                       class="nav-link {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-data nav-icon"></i>
                        <span class="nav-text">Laporan Stok</span>
                    </a>
                </li>
            </ul>

            <div class="sidebar-divider"></div>

            {{-- PENGATURAN (Admin Only) --}}
            <span class="sidebar-label">Sistem</span>
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                       class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people nav-icon"></i>
                        <span class="nav-text">Manajemen User</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}"
                       class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear nav-icon"></i>
                        <span class="nav-text">Pengaturan Toko</span>
                    </a>
                </li>
            </ul>
            @endif

        </div>

        {{-- Sidebar Footer: User Info --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-user-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="sidebar-user-info">
                    <span class="sidebar-user-name">{{ auth()->user()->name }}</span>
                    <span class="sidebar-user-role">{{ auth()->user()->role_label }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="sidebar-logout-btn"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title="Keluar">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>{{-- /app-sidebar --}}

    {{-- ================================================================
         MAIN CONTENT
         ================================================================ --}}
    <div class="main-content">

        {{-- TOP NAVBAR --}}
        <nav class="app-navbar">
            {{-- Hamburger (mobile) --}}
            <button class="navbar-toggle-btn" data-sidebar-toggle aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>

            {{-- Page Title --}}
            <h1 class="navbar-page-title">{{ $pageTitle ?? ($title ?? 'Dashboard') }}</h1>

            {{-- Actions --}}
            <div class="navbar-actions">

                {{-- Stock Alert Bell --}}
                @php
                    $alertCount = \App\Models\Product::where('is_active', true)
                        ->whereColumn('stock', '<=', 'min_stock')
                        ->count();
                @endphp

                <a href="{{ route('products.index') }}?filter=low_stock"
                   class="navbar-icon-btn"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   title="{{ $alertCount > 0 ? $alertCount . ' produk stok menipis' : 'Semua stok aman' }}">
                    <i class="bi bi-bell"></i>
                    @if($alertCount > 0)
                        <span class="navbar-badge"></span>
                    @endif
                </a>

                {{-- Kasir Shortcut --}}
                <a href="{{ route('kasir.index') }}"
                   class="navbar-icon-btn"
                   data-bs-toggle="tooltip"
                   data-bs-placement="bottom"
                   title="Buka Kasir">
                    <i class="bi bi-cart-plus"></i>
                </a>

                {{-- User Dropdown --}}
                <div class="dropdown">
                    <button class="navbar-user-btn dropdown-toggle"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            style="border: none; background: none;">
                        <div class="navbar-user-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <span class="navbar-user-name">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <h6 class="dropdown-header" style="font-size:0.7rem;">
                                {{ auth()->user()->role_label }}
                            </h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person me-2 text-secondary"></i>Edit Profil
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                        <li>
                            <a class="dropdown-item" href="{{ route('settings.index') }}">
                                <i class="bi bi-gear me-2 text-secondary"></i>Pengaturan
                            </a>
                        </li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>{{-- /app-navbar --}}

        {{-- PAGE CONTENT --}}
        <main class="page-content">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible alert-auto-hide fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible alert-auto-hide fade show mb-4" role="alert">
                    <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible alert-auto-hide fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible alert-auto-hide fade show mb-4" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Slot: Page Content --}}
            {{ $slot }}

        </main>

    </div>{{-- /main-content --}}

</div>{{-- /app-wrapper --}}

{{-- Toast Container --}}
<div id="toastContainer" class="toast-container"></div>

@stack('scripts')

</body>
</html>
