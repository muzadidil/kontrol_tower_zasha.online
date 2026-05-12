@extends('layouts.admin')

@section('content')
<style>
    .role-card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-bottom: 16px; }
    .role-default-badge { background: #dbeafe; color: #1e40af; padding: 2px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
    .feature-chip { display: inline-block; background: #f0fdf4; color: #065f46; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; margin: 2px; }
    .no-feature { color: #9ca3af; font-style: italic; font-size: 12px; }
</style>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Manajemen Role Mitra</h4>
            <small class="text-muted">Atur role & fitur yang bisa diakses mitra.</small>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Tambah Role
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @forelse($roles as $role)
        <div class="role-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="fw-bold mb-1">
                        {{ $role->name }}
                        @if($role->is_default)
                            <span class="role-default-badge ms-2">Default</span>
                        @endif
                    </h5>
                    <div class="text-muted small">{{ $role->description ?? '—' }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @unless($role->is_default)
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Hapus role ini? Mitra yg pakai role ini akan kehilangan akses.')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endunless
                </div>
            </div>

            <div class="mt-3">
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

            <div class="mt-3 small text-muted">
                <i class="bi bi-people-fill me-1"></i>
                {{ \App\Models\Mitra::where('role_id', $role->id)->count() }} mitra menggunakan role ini
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
