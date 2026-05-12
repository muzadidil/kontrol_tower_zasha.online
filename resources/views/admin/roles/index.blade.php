@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')

<style>
    .role-row-card { position: relative; border-left: 3px solid transparent; }
    .role-row-card.draft  { border-left-color: var(--zasha-warning); background: linear-gradient(90deg, #fffbeb 0%, transparent 200px); }
    .role-row-card.active-role { border-left-color: var(--zasha-success); }

    .role-features-tags {
        display: flex; flex-wrap: wrap; gap: 5px;
        margin-top: 8px;
    }
    .role-feat-tag {
        font-size: 0.66rem;
        background: var(--zasha-gray-100); color: var(--zasha-gray-700);
        padding: 2px 8px; border-radius: 6px;
        font-weight: 600;
    }
    .role-feat-tag.more { background: var(--zasha-blue-light); color: var(--zasha-blue); }

    .btn-zasha {
        border-radius: 999px;
        padding: 6px 14px;
        font-size: 0.74rem;
        font-weight: 700;
        border: 1.5px solid;
        white-space: nowrap;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-zasha.primary  { background: var(--zasha-blue); border-color: var(--zasha-blue); color: white; }
    .btn-zasha.primary:hover { background: var(--zasha-blue-dark); border-color: var(--zasha-blue-dark); color: white; }
    .btn-zasha.success  { background: var(--zasha-success); border-color: var(--zasha-success); color: white; }
    .btn-zasha.success:hover { background: #059669; color: white; }
    .btn-zasha.outline-warning { background: white; border-color: var(--zasha-warning); color: #92400e; }
    .btn-zasha.outline-warning:hover { background: var(--zasha-warning); color: white; }
    .btn-zasha.outline-primary { background: white; border-color: var(--zasha-blue); color: var(--zasha-blue); }
    .btn-zasha.outline-primary:hover { background: var(--zasha-blue); color: white; }
    .btn-zasha.outline-danger { background: white; border-color: var(--zasha-danger); color: var(--zasha-danger); }
    .btn-zasha.outline-danger:hover { background: var(--zasha-danger); color: white; }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-shield-lock-fill"></i> Role & Akses Mitra</h4>
        <div class="zasha-page-subtitle">Atur role & fitur yang bisa diakses mitra. Role baru harus dirilis dulu.</div>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn-zasha primary">
        <i class="bi bi-plus-lg"></i> Tambah Role
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 small" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger rounded-3 small">{{ session('error') }}</div>
@endif

<div class="zasha-card">
    <div class="zasha-list-header">
        <h6><i class="bi bi-list-ul"></i> Daftar Role</h6>
        <span class="badge-count">{{ $roles->count() }}</span>
    </div>

    @forelse($roles as $role)
        @php
            $iconClass = $role->icon ?: \App\Models\Role::DEFAULT_ICON;
            $iconColor = $role->icon_color ?: \App\Models\Role::DEFAULT_ICON_COLOR;
            $iconBg = $iconColor . '20';
            $mitraCount = \App\Models\Mitra::where('role_id', $role->id)->count();
            $features = $role->features;
            $featuresShown = $features->take(4);
            $featuresMore  = $features->count() - 4;
        @endphp
        <div class="zasha-row role-row-card {{ $role->is_active ? 'active-role' : 'draft' }}">
            {{-- Icon --}}
            <div class="zasha-row-icon" style="background:{{ $iconBg }}; color:{{ $iconColor }};">
                <i class="bi {{ $iconClass }}"></i>
            </div>

            {{-- Body --}}
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    {{ $role->name }}
                    @if($role->is_default)
                        <span class="zasha-status-badge purple ms-1" style="font-size:0.6rem;">Default</span>
                    @endif
                </div>
                <div class="zasha-row-meta">
                    @if($role->is_active)
                        <span class="zasha-status-badge success"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                    @else
                        <span class="zasha-status-badge pending"><i class="bi bi-pencil-fill"></i> Draft</span>
                    @endif
                    <span><i class="bi bi-people-fill"></i> {{ $mitraCount }} mitra</span>
                    @if($role->description)
                        <span class="d-none d-md-inline">· {{ Str::limit($role->description, 50) }}</span>
                    @endif
                </div>
                @if($features->isNotEmpty())
                    <div class="role-features-tags">
                        @foreach($featuresShown as $f)
                            <span class="role-feat-tag">{{ \App\Models\Role::ALL_FEATURES[$f->feature_key] ?? $f->feature_key }}</span>
                        @endforeach
                        @if($featuresMore > 0)
                            <span class="role-feat-tag more">+{{ $featuresMore }} fitur lain</span>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Action buttons (kompak, sebaris dengan tulisan) --}}
            <div class="d-flex gap-1 align-items-center flex-shrink-0">
                @if(!$role->is_active)
                    <form action="{{ route('admin.roles.toggleActive', $role->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-zasha success"
                                onclick="return confirm('Rilis & aktifkan role ini?')">
                            <i class="bi bi-rocket-takeoff-fill"></i> Rilis
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.roles.toggleActive', $role->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn-zasha outline-warning"
                                onclick="return confirm('Set role ini sebagai DRAFT?')">
                            <i class="bi bi-pause-circle"></i> Draft
                        </button>
                    </form>
                @endif

                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn-zasha outline-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                @unless($role->is_default)
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-zasha outline-danger"
                                onclick="return confirm('Hapus role ini? Mitra yg pakai role ini akan kehilangan akses.')">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                @endunless
            </div>
        </div>
    @empty
        <div class="zasha-empty">
            <i class="bi bi-shield-x"></i>
            <div class="zasha-empty-title">Belum ada role</div>
            <div class="zasha-empty-sub">Klik "Tambah Role" untuk membuat role pertama.</div>
        </div>
    @endforelse
</div>
@endsection
