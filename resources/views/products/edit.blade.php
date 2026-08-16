<x-app-layout>
    <x-slot name="title">Edit Barang</x-slot>
    <x-slot name="pageTitle">Edit Barang</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Barang</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Edit Barang: {{ $product->name }}</h2>
            <p class="page-header-subtitle">Perbarui informasi barang, harga, dan preferensi lainnya.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('products.update', $product->id) }}" method="POST" data-loading>
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-12 col-xl-8">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Informasi Dasar</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                Kode Barang: <strong>{{ $product->code }}</strong> (Kode digenerate otomatis dan tidak bisa diubah)
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="" disabled>-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="unit" class="form-label">Satuan <span class="text-danger">*</span></label>
                                <select class="form-select @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                    <option value="" disabled>-- Pilih Satuan --</option>
                                    @php
                                        $units = ['Pcs', 'Kg', 'Liter', 'Karung', 'Botol', 'Box', 'Pack', 'Bungkus', 'Galon', 'Sak', 'Lusin'];
                                        $currentUnit = old('unit', $product->unit);
                                    @endphp
                                    @foreach($units as $u)
                                        <option value="{{ $u }}" {{ $currentUnit == $u ? 'selected' : '' }}>{{ $u }}</option>
                                    @endforeach
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label">Keterangan (Opsional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Harga & Stok</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="purchase_price" class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', (int)$product->purchase_price) }}" min="0" required>
                                        </div>
                                        @error('purchase_price')
                                            <div class="text-danger mt-1" style="font-size: 0.875em;">{{ $message }}</div>
                                        @enderror
                                    </div>
        
                                    <div class="col-12">
                                        <label for="selling_price" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', (int)$product->selling_price) }}" min="0" required>
                                        </div>
                                        @error('selling_price')
                                            <div class="text-danger mt-1" style="font-size: 0.875em;">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center mb-1">
                                            <div>
                                                <div class="text-muted" style="font-size: 0.75rem;">Stok Saat Ini</div>
                                                <div class="fw-bold fs-5">{{ $product->stock }} <span class="fs-6 fw-normal text-muted">{{ $product->unit }}</span></div>
                                            </div>
                                            <a href="{{ route('stock.adjustments', ['product_id' => $product->id]) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Gunakan menu penyesuaian stok untuk mengubah stok.">Sesuaikan</a>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.75rem;">Untuk mengubah stok, gunakan fitur Penyesuaian Stok.</small>
                                    </div>

                                    <div class="col-12">
                                        <label for="min_stock" class="form-label mt-2">Stok Minimum <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required>
                                        @error('min_stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Aktifkan Barang</label>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Barang nonaktif tidak akan muncul di menu Kasir POS.</small>
                                </div>
        
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                    <a href="{{ route('products.index') }}" class="btn btn-light">Batal</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</x-app-layout>
