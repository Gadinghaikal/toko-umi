<x-app-layout>
    <x-slot name="title">Edit Profil</x-slot>
    <x-slot name="pageTitle">Edit Profil</x-slot>

    <div class="page-header">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active">Edit Profil</li>
            </ol>
        </nav>
        <h2 class="page-header-title">Edit Profil</h2>
        <p class="page-header-subtitle">Kelola informasi akun dan password Anda</p>
    </div>

    <div class="row g-4">

        {{-- Update Profile Info --}}
        <div class="col-12 col-lg-7">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Informasi Profil
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" data-loading>
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   autofocus
                                   autocomplete="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                            </button>
                            @if(session('status') === 'profile-updated')
                                <span class="text-success" style="font-size:0.8rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Berhasil disimpan!
                                </span>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Update Password --}}
        <div class="col-12 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-key me-2 text-warning"></i>Ubah Password
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}" data-loading>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password"
                                   id="new_password"
                                   name="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-shield-check me-1"></i>Ubah Password
                            </button>
                            @if(session('status') === 'password-updated')
                                <span class="text-success" style="font-size:0.8rem;">
                                    <i class="bi bi-check-circle-fill me-1"></i>Password diperbarui!
                                </span>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
