<x-app-layout>
    <x-slot name="title">Pengaturan Toko</x-slot>
    <x-slot name="pageTitle">Pengaturan Toko</x-slot>

    <div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pengaturan</li>
                </ol>
            </nav>
            <h2 class="page-header-title">Konfigurasi Sistem</h2>
            <p class="page-header-subtitle">Sesuaikan informasi toko, kasir, laporan, dan tampilan aplikasi.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-10">
            <div class="card">
                <div class="card-header bg-light d-flex align-items-center gap-2">
                    <i class="bi bi-sliders text-primary"></i>
                    <h5 class="mb-0 fw-600" style="font-size: 0.95rem;">Form Pengaturan</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST" data-loading>
                        @csrf
                        @method('PUT')

                        <ul class="nav nav-tabs mb-4" id="settingTabs" role="tablist">
                            @foreach($settings as $group => $items)
                                @php
                                    $groupName = match($group) {
                                        'general' => 'Informasi Toko',
                                        'pos' => 'Kasir POS',
                                        'report' => 'Laporan',
                                        'appearance' => 'Tampilan',
                                        default => ucfirst($group)
                                    };
                                    $groupIcon = match($group) {
                                        'general' => 'bi-shop',
                                        'pos' => 'bi-cart-check',
                                        'report' => 'bi-file-earmark-bar-graph',
                                        'appearance' => 'bi-palette',
                                        default => 'bi-gear'
                                    };
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $group }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $group }}-tab-pane" type="button" role="tab" aria-controls="{{ $group }}-tab-pane" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        <i class="bi {{ $groupIcon }} me-1"></i> {{ $groupName }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content" id="settingTabsContent">
                            @foreach($settings as $group => $items)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $group }}-tab-pane" role="tabpanel" aria-labelledby="{{ $group }}-tab" tabindex="0">
                                    <div class="row g-4">
                                        @foreach($items as $setting)
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label for="{{ $setting->key }}" class="form-label fw-bold mb-0">{{ $setting->label }}</label>
                                                        @if($setting->description)
                                                            <div class="form-text mt-1 text-muted">{{ $setting->description }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-8">
                                                        @if($setting->type === 'text' || $setting->type === 'number')
                                                            <input type="{{ $setting->type }}" class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" value="{{ old($setting->key, $setting->value) }}">
                                                        
                                                        @elseif($setting->type === 'textarea')
                                                            <textarea class="form-control" id="{{ $setting->key }}" name="{{ $setting->key }}" rows="3">{{ old($setting->key, $setting->value) }}</textarea>
                                                        
                                                        @elseif($setting->type === 'boolean')
                                                            <div class="form-check form-switch mt-1">
                                                                <input class="form-check-input" type="checkbox" role="switch" id="{{ $setting->key }}" name="{{ $setting->key }}" value="1" {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }} style="width: 2.5em; height: 1.25em;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @if(!$loop->last)
                                                    <hr class="border-light mt-4">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="reset" class="btn btn-light me-2">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
