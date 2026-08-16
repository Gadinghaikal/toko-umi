<x-app-layout>
    <x-slot name="title">Tambah Kategori</x-slot>
    <x-slot name="pageTitle">Tambah Kategori</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('categories.index') }}" class="text-decoration-none">Kategori</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Tambah Kategori Baru</h2>
            <p class="page-header-subtitle">Silakan isi formulir di bawah ini untuk menambahkan kategori baru.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Formulir Kategori</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.store') }}" method="POST" data-loading>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Sembako, Pupuk" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Keterangan (Opsional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Tambahkan keterangan jika perlu">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan Kategori ini</label>
                            </div>
                            <small class="text-muted" style="font-size: 0.75rem;">Kategori nonaktif tidak akan muncul saat menambah/mengedit produk.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Kategori</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
