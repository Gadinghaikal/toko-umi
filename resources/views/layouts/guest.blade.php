<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="TOKO UMI — Sistem Informasi Penjualan & Inventaris">

    <title>{{ $title ?? config('app.name', 'TOKO UMI') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')

    <style>
        /* Default guest page background */
        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A5F 50%, #0F172A 100%);
            min-height: 100vh;
        }

        /* When login page sets lp-wrap, remove the body gradient */
        body:has(.lp-wrap) {
            background: none !important;
        }
    </style>
</head>
<body>

    {{-- Login page uses its own full layout (.lp-wrap) --}}
    @if($slot->isNotEmpty())
        {{ $slot }}
    @endif

    {{-- Toast Container --}}
    <div id="toastContainer" class="toast-container"></div>

    @stack('scripts')

</body>
</html>
