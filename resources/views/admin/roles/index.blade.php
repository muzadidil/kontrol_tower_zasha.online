@extends('layouts.admin')

@section('content')
<style>
    .role-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        margin-bottom: 16px;
        position: relative;
        transition: all 0.2s ease;
    }
    .role-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .role-card.draft { border-left: 4px solid #f59e0b; background: #fffbeb; }
    .role-card.active { border-left: 4px solid #10b981; }

    .role-icon-large {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem; flex-shrink: 0;
    }

    .badge-status {
        padding: 3px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .badge-active   { background: #d1fae5; color: #065f46; }
    .badge-draft    { background: #fef3c7; color: #92400e; }
    .badge-default  { background: #dbeafe; color: #1e40af; }

    .feature-chip {
        display: inline-block;
        background: #f0fdf4; color: #065f46;
        padding: 3px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 600; margin: 2px;
    }
    .no-feature { color: #9ca3af; font-style: italic; font-size: 12px; }

    .btn-rilis {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white !important; border: none;
        padding: 8px 18px; border-radius: 999px;
        font-size: 12px; font-weight: 800;
        transition: all 0.2s; cursor: pointer;
        box-shadow: 0 2px 8px rgba(16,185,129,0.3);
    }
    .btn-rilis:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,185,129,0.4); }

    .btn-draft {
        background: white; color: #92400e !important;
        border: 1.5px solid #f59e0b;
        padding: 8px 18px; border-radius: 999px;
        font-size: 12px; font-weight: 800; cursor: pointer;
    }
</style>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Manajemen Role Mitra</h4>
            <small class="text-muted">Atur role & fitur yang bisa diakses mitra. Role baru harus dirilis dulu sebelum bisa di-assign ke mitra.</small>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Tambah Role
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse($roles as $role)
        @php
            $iconClass = $role->icon ?: \App\Models\Role::DEFAULT_ICON;
            $iconColor = $role->icon_color ?: \App\Models\Role::DEFAULT_ICON_COLOR;
            // Buat warna background dengan opacity rendah dari icon_color
            $iconBg = $iconColor . '20'; // 20 = ~12% opacity in hex
        @endphp
        <div class="role-card {{ $role->is_active ? 'active' : 'draft' }}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    {{-- Icon --}}
                    <div class="role-icon-large" style="background: {{ $iconBg }}; color: {{ $iconColor }};">
                        <i class="bi {{ $iconClass }}"></i>
                    </div>
                    {{-- Info --}}
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                            <h5 class="fw-bold mb-0">{{ $role->name }}</h5>
                            @if($role->is_active)
                                <span class="badge-status badge-active">
                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge-status badge-draft">
                                    <i class="bi bi-pencil-fill me-1"></i>Draft
                                </span>
                            @endif
                            @if($role->is_default)
                                <span class="badge-status badge-default">Default</span>
                            @endif
                        </div>
                        <div class="text-muted small">{{ $role->description ?? '—' }}</div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 align-items-center flex-shrink-0">
                    @if(!$role->is_active)
                        <form action="{{ route('admin.roles.toggleActive', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-rilis"
                                    onclick="return confirm('Rilis & aktifkan role ini? Setelah aktif, role bisa di-assign ke mitra.')">
                                <i class="bi bi-rocket-takeoff-fill me-1"></i> Rilis & Aktifkan
                            </button>
                        </form>
                    @else
                        <form action="{{ route('admin.roles.toggleActive', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-draft" title="Set sebagai draft (sembunyikan)"
                                    onclick="return confirm('Set role ini sebagai DRAFT? Role tidak akan tersedia untuk di-assign ke mitra baru.')">
                                <i class="bi bi-pause-circle me-1"></i> Set Draft
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @unless($role->is_default)
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill"
                                    onclick="return confirm('Hapus role ini? Mitra yg pakai role ini akan kehilangan akses.')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endunless
                </div>
            </div>

            <div class="mt-3 pt-3 border-top">
                <div class="text-muted small fw-bold mb-2">FITUR YANG BISA DIAKSES:</div>
                @if($role->features->isEmpty())
                    <span class="no-feature">Belum ada fitur — mitra tidak akan bisa akses apapun.</span>
                @else
                    @foreach($role->features as $feature)
                        <span class="feature-chip">
                            <i class="bi bi-check-circle-fill me-1"></i>
                            {{ \App\Models\Role::ALL_FEATURES[$feature->feature_key] ?? $feature->feature_key }}
                        </span>
                    @endforeach
                @endif
            </div>

            <div class="mt-3 small text-muted d-flex justify-content-between">
                <div>
                    <i class="bi bi-people-fill me-1"></i>
                    {{ \App\Models\Mitra::where('role_id', $role->id)->count() }} mitra menggunakan role ini
                </div>
                @if(!$role->is_active)
                    <div class="text-warning fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Role ini masih DRAFT — tidak muncul saat assign mitra
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-shield-x display-1"></i>
            <p class="mt-3">Belum ada role.</p>
        </div>
    @endforelse
</div>
@endsection
