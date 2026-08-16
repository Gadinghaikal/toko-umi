<x-app-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>
    <x-slot name="pageTitle">Manajemen Pengguna</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                    <li class="breadcrumb-item active">Pengguna</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Manajemen Akun Kasir & Admin</h2>
            <p class="page-header-subtitle">Kelola akses staf untuk menggunakan aplikasi ini.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Daftar Pengguna</h5>
            <form action="{{ route('users.index') }}" method="GET" class="d-flex gap-2">
                <select name="role" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 130px;">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="kasir" {{ request('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                </select>
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-center" style="width: 50px;">No</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Email</th>
                            <th scope="col" class="text-center">Peran</th>
                            <th scope="col" class="text-center">Status</th>
                            <th scope="col" class="text-center" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr>
                                <td class="text-center">{{ $users->firstItem() + $index }}</td>
                                <td class="fw-600">
                                    {{ $user->name }}
                                    @if(auth()->id() === $user->id)
                                        <span class="badge bg-primary-soft text-primary ms-1">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td class="text-center">
                                    @if ($user->role === 'admin')
                                        <span class="badge bg-dark text-white"><i class="bi bi-shield-lock me-1"></i> Admin</span>
                                    @else
                                        <span class="badge bg-info-soft text-info"><i class="bi bi-person me-1"></i> Kasir</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($user->is_active)
                                        <span class="badge bg-success-soft text-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger-soft text-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" data-loading>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" data-bs-toggle="tooltip" title="Hapus" data-confirm-delete="Apakah Anda yakin ingin menghapus akun '{{ $user->name }}'?">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state py-4">
                                        <i class="bi bi-people empty-state-icon"></i>
                                        <p class="empty-state-title">Tidak ada pengguna ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</x-app-layout>
