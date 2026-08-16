<x-app-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>
    <x-slot name="pageTitle">Tambah Pengguna</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none">Pengguna</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Tambah Staf Baru</h2>
            <p class="page-header-subtitle">Berikan akses ke sistem untuk admin atau kasir baru.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.store') }}" method="POST" data-loading>
                        @csrf

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">Email Login <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="role" class="form-label">Peran (Role) <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="" disabled selected>-- Pilih Peran --</option>
                                    <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir (Akses Penjualan)</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <div class="form-check form-switch border p-3 rounded bg-light">
                                    <input class="form-check-input ms-0 me-3 mt-1" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                    <label class="form-check-label fw-600" for="is_active">Aktifkan Akun</label>
                                    <small class="text-muted d-block mt-1">Akun yang dinonaktifkan tidak akan bisa melakukan login ke sistem.</small>
                                </div>
                            </div>

                            <div class="col-12 mt-4 d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-light">Reset</button>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Pengguna</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
