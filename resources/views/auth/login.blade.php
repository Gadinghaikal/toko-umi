<x-guest-layout>
    <x-slot name="title">Login — TOKO UMI</x-slot>

    <div class="lp-wrap">

        {{-- ===== LEFT PANEL: Branding ===== --}}
        <div class="lp-brand">
            {{-- Decorative orbs --}}
            <div class="lp-orb lp-orb-1"></div>
            <div class="lp-orb lp-orb-2"></div>
            <div class="lp-orb lp-orb-3"></div>

            <div class="lp-brand-inner">
                <div class="lp-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo TOKO UMI">
                </div>

                <h1 class="lp-brand-title">TOKO UMI</h1>
                <p class="lp-brand-desc">Sistem Informasi Penjualan<br>&amp; Manajemen Inventaris</p>

                <div class="lp-features">
                    <div class="lp-feature-item">
                        <span class="lp-feature-icon"><i class="bi bi-graph-up-arrow"></i></span>
                        <span>Pantau penjualan secara real-time</span>
                    </div>
                    <div class="lp-feature-item">
                        <span class="lp-feature-icon"><i class="bi bi-boxes"></i></span>
                        <span>Kelola stok &amp; inventaris mudah</span>
                    </div>
                    <div class="lp-feature-item">
                        <span class="lp-feature-icon"><i class="bi bi-receipt-cutoff"></i></span>
                        <span>Laporan transaksi lengkap &amp; akurat</span>
                    </div>
                </div>
            </div>

            <p class="lp-brand-footer">&copy; {{ date('Y') }} TOKO UMI &mdash; Versi 1.0</p>
        </div>

        {{-- ===== RIGHT PANEL: Form ===== --}}
        <div class="lp-form-panel">
            <div class="lp-form-wrap">

                {{-- Mobile-only logo --}}
                <div class="lp-mobile-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo TOKO UMI">
                    <span>TOKO UMI</span>
                </div>

                <div class="lp-form-header">
                    <h2>Selamat Datang 👋</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div class="lp-alert lp-alert-danger">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="lp-alert lp-alert-danger">
                        <i class="bi bi-x-circle-fill"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login') }}" data-loading>
                    @csrf

                    {{-- Email --}}
                    <div class="lp-field @error('email') is-error @enderror">
                        <label for="email">Alamat Email</label>
                        <div class="lp-input-wrap">
                            <span class="lp-input-icon"><i class="bi bi-envelope"></i></span>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="admin@tokoumi.com"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        @error('email')
                            <span class="lp-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="lp-field @error('password') is-error @enderror">
                        <div class="lp-field-top">
                            <label for="password">Password</label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="lp-forgot">Lupa password?</a>
                            @endif
                        </div>
                        <div class="lp-input-wrap">
                            <span class="lp-input-icon"><i class="bi bi-lock"></i></span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" id="togglePassword" class="lp-toggle-eye" tabindex="-1" aria-label="Tampilkan password">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="lp-field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="lp-remember">
                        <label class="lp-check-label">
                            <input type="checkbox" id="remember_me" name="remember">
                            <span class="lp-check-box"></span>
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="loginBtn" class="lp-btn-submit">
                        <span class="lp-btn-text">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Masuk
                        </span>
                        <span class="lp-btn-loader" aria-hidden="true">
                            <span></span><span></span><span></span>
                        </span>
                    </button>

                </form>

            </div>
        </div>

    </div>

    <style>
        /* ============================================================
           LOGIN PAGE — Modern Split Layout
           ============================================================ */

        /* Reset guest layout defaults for this page */
        body { background: #F0F4F8 !important; }

        .lp-wrap {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ── LEFT BRAND PANEL ── */
        .lp-brand {
            position: relative;
            width: 45%;
            background: linear-gradient(145deg, #0D2137 0%, #1E3A5F 45%, #2D5A27 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px 52px;
            overflow: hidden;
        }

        /* Decorative orbs */
        .lp-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            pointer-events: none;
        }
        .lp-orb-1 {
            width: 280px; height: 280px;
            background: rgba(45, 90, 39, 0.35);
            top: -80px; left: -60px;
        }
        .lp-orb-2 {
            width: 200px; height: 200px;
            background: rgba(30, 58, 95, 0.5);
            bottom: 120px; right: -40px;
        }
        .lp-orb-3 {
            width: 150px; height: 150px;
            background: rgba(238, 108, 43, 0.2);
            bottom: -30px; left: 80px;
        }

        .lp-brand-inner {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .lp-logo {
            width: 72px; height: 72px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 28px;
            backdrop-filter: blur(8px);
        }
        .lp-logo img {
            width: 44px; height: 44px; object-fit: contain;
        }

        .lp-brand-title {
            font-size: 2.6rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.04em;
            line-height: 1;
            margin: 0 0 14px;
        }

        .lp-brand-desc {
            font-size: 1rem;
            color: rgba(255,255,255,0.60);
            line-height: 1.65;
            margin: 0 0 48px;
        }

        .lp-features {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .lp-feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(255,255,255,0.75);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .lp-feature-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: rgba(255,255,255,0.10);
            border: 1px solid rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            color: rgba(255,255,255,0.85);
            flex-shrink: 0;
        }

        .lp-brand-footer {
            position: relative;
            z-index: 1;
            font-size: 0.72rem;
            color: rgba(255,255,255,0.3);
            margin: 0;
        }

        /* ── RIGHT FORM PANEL ── */
        .lp-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
            background: #F8FAFC;
        }

        .lp-form-wrap {
            width: 100%;
            max-width: 400px;
        }

        /* Mobile-only logo */
        .lp-mobile-logo {
            display: none;
            align-items: center;
            gap: 10px;
            margin-bottom: 32px;
        }
        .lp-mobile-logo img { width: 36px; height: 36px; object-fit: contain; }
        .lp-mobile-logo span { font-size: 1.1rem; font-weight: 800; color: #0D2137; }

        .lp-form-header {
            margin-bottom: 32px;
        }
        .lp-form-header h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0D2137;
            letter-spacing: -0.03em;
            margin: 0 0 6px;
        }
        .lp-form-header p {
            font-size: 0.875rem;
            color: #64748B;
            margin: 0;
        }

        /* ── ALERTS ── */
        .lp-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.82rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .lp-alert i { font-size: 0.95rem; flex-shrink: 0; margin-top: 1px; }
        .lp-alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }

        /* ── FIELDS ── */
        .lp-field {
            margin-bottom: 20px;
        }
        .lp-field-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 7px;
        }
        .lp-field > label,
        .lp-field-top label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #374151;
            letter-spacing: 0.01em;
            margin-bottom: 7px;
        }
        .lp-field-top label { margin-bottom: 0; }

        .lp-forgot {
            font-size: 0.75rem;
            color: #EE6C2B;
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.15s;
        }
        .lp-forgot:hover { opacity: 0.75; }

        .lp-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .lp-input-icon {
            position: absolute;
            left: 14px;
            top: 50%; transform: translateY(-50%);
            color: #94A3B8;
            font-size: 0.9rem;
            pointer-events: none;
            transition: color 0.2s;
        }

        .lp-input-wrap input {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 42px;
            border: 1.5px solid #E2E8F0;
            border-radius: 12px;
            background: #FFFFFF;
            font-size: 0.875rem;
            font-family: inherit;
            color: #1E293B;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .lp-input-wrap input::placeholder { color: #CBD5E1; }

        .lp-input-wrap input:focus {
            border-color: #2D5A27;
            box-shadow: 0 0 0 4px rgba(45, 90, 39, 0.10);
        }
        .lp-input-wrap input:focus ~ .lp-input-icon,
        .lp-field .lp-input-wrap:focus-within .lp-input-icon {
            color: #2D5A27;
        }

        .lp-field.is-error .lp-input-wrap input {
            border-color: #F87171;
            box-shadow: 0 0 0 4px rgba(248, 113, 113, 0.12);
        }

        .lp-field-error {
            display: block;
            font-size: 0.72rem;
            color: #DC2626;
            margin-top: 5px;
            font-weight: 500;
        }

        .lp-toggle-eye {
            position: absolute;
            right: 12px;
            top: 50%; transform: translateY(-50%);
            background: none;
            border: none;
            padding: 4px;
            color: #94A3B8;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.95rem;
            display: flex; align-items: center; justify-content: center;
            transition: color 0.15s, background 0.15s;
        }
        .lp-toggle-eye:hover { color: #475569; background: #F1F5F9; }

        /* ── REMEMBER ME ── */
        .lp-remember {
            margin-bottom: 24px;
        }
        .lp-check-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 0.8rem;
            color: #64748B;
            font-weight: 500;
            user-select: none;
        }
        .lp-check-label input[type="checkbox"] { display: none; }

        .lp-check-box {
            width: 18px; height: 18px;
            border: 1.5px solid #CBD5E1;
            border-radius: 5px;
            background: #FFF;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
            position: relative;
        }
        .lp-check-label input:checked + .lp-check-box {
            background: #2D5A27;
            border-color: #2D5A27;
        }
        .lp-check-label input:checked + .lp-check-box::after {
            content: '';
            width: 10px; height: 6px;
            border-left: 2px solid #fff;
            border-bottom: 2px solid #fff;
            transform: rotate(-45deg) translateY(-1px);
            display: block;
        }

        /* ── SUBMIT BUTTON ── */
        .lp-btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #2D5A27 0%, #3D7A33 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(45,90,39,0.35);
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }
        .lp-btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.2s;
        }
        .lp-btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(45,90,39,0.4); }
        .lp-btn-submit:hover::before { background: rgba(255,255,255,0.06); }
        .lp-btn-submit:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(45,90,39,0.3); }

        .lp-btn-text { display: flex; align-items: center; gap: 8px; }

        /* Dot loader */
        .lp-btn-loader {
            display: none;
            gap: 5px;
        }
        .lp-btn-loader span {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: rgba(255,255,255,0.85);
            animation: lpDot 1.2s infinite ease-in-out;
        }
        .lp-btn-loader span:nth-child(2) { animation-delay: 0.15s; }
        .lp-btn-loader span:nth-child(3) { animation-delay: 0.3s; }

        @keyframes lpDot {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.5; }
            40%            { transform: scale(1.0); opacity: 1; }
        }

        .lp-btn-submit.is-loading .lp-btn-text { display: none; }
        .lp-btn-submit.is-loading .lp-btn-loader { display: flex; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .lp-brand { width: 40%; padding: 40px 36px; }
            .lp-brand-title { font-size: 2rem; }
        }

        @media (max-width: 680px) {
            .lp-wrap { flex-direction: column; }

            .lp-brand { display: none; }

            .lp-form-panel { padding: 32px 20px; align-items: flex-start; padding-top: 48px; }

            .lp-mobile-logo { display: flex; }

            .lp-form-wrap { max-width: 100%; }
        }
    </style>

    @push('scripts')
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon  = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        // Loading state on submit
        document.querySelector('form[data-loading]').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('is-loading');
            btn.disabled = true;
        });
    </script>
    @endpush
</x-guest-layout>
