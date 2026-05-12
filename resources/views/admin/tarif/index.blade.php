@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')

<style>
    .stat-card { background: #fff; border-radius: 14px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .stat-card .label { font-size: .7rem; color: #64748b; text-transform: uppercase; font-weight: 600; letter-spacing: .03em; }
    .stat-card .value { font-size: 1.4rem; font-weight: 700; color: #1e293b; }
    .stat-card .hint  { font-size: .65rem; color: #94a3b8; }

    .tarif-row { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 12px; }
    .tarif-row:last-child { border-bottom: none; }
    .tarif-row:hover { background: #f8fafc; }

    .grouped-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.04); margin-bottom: 16px; overflow: hidden; }
    .grouped-card .head { padding: 14px 18px; background: linear-gradient(135deg, #eff6ff, #fff); border-bottom: 1px solid #e2e8f0; }
    .price-range { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #fffbeb; color: #ca8a04; border-radius: 999px; font-size: .7rem; font-weight: 600; }

    .toggle-tab { display: inline-flex; padding: 4px; background: #f1f5f9; border-radius: 999px; gap: 2px; }
    .toggle-tab a { padding: 6px 16px; border-radius: 999px; font-size: .75rem; font-weight: 600; color: #64748b; text-decoration: none; transition: all .2s; }
    .toggle-tab a.active { background: #fff; color: #005aa9; box-shadow: 0 1px 2px rgba(0,0,0,.05); }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-cash-coin"></i> Tarif Layanan Mitra</h4>
        <div class="zasha-page-subtitle">Banding harga antar mitra. Komisi Zasha: {{ number_format($komisiPersen, 1) }}%</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3 small">{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Total Tarif</div>
            <div class="value">{{ $stats['total_tarif'] }}</div>
            <div class="hint">item terdaftar</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Mitra Terlibat</div>
            <div class="value">{{ $stats['total_mitra'] }}</div>
            <div class="hint">mitra punya tarif</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Jenis Layanan</div>
            <div class="value">{{ $stats['total_layanan'] }}</div>
            <div class="hint">layanan unik</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="label">Rata-rata</div>
            <div class="value" style="font-size:1.1rem;">Rp {{ number_format($stats['rata_harga'], 0, ',', '.') }}</div>
            <div class="hint">harga tarif mitra</div>
        </div>
    </div>
</div>

{{-- Filter & Toggle View --}}
<form method="GET" action="{{ route('admin.tarif.index') }}" class="zasha-card mb-3" style="padding:16px;">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold mb-1">Cari Layanan / Mitra</label>
            <input type="text" name="q" value="{{ $filterKeyword }}" class="form-control form-control-sm rounded-pill"
                   placeholder="contoh: Service AC atau nama mitra">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold mb-1">Role</label>
            <select name="role_id" class="form-select form-select-sm rounded-pill">
                <option value="">Semua Role</option>
                <option value="none" {{ ($filterRoleId ?? '') === 'none' ? 'selected' : '' }}>— Tanpa Role —</option>
                @foreach($rolesForFilter as $r)
                    <option value="{{ $r->id }}" {{ (string)($filterRoleId ?? '') === (string)$r->id ? 'selected' : '' }}>
                        {{ $r->name }} @if(!$r->is_active) [DRAFT] @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold mb-1">Status</label>
            <select name="status" class="form-select form-select-sm rounded-pill">
                <option value="">Semua</option>
                <option value="aktif" {{ $filterStatus === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ $filterStatus === 'nonaktif' ? 'selected' : '' }}>Non-aktif</option>
            </select>
        </div>
        <input type="hidden" name="group" value="{{ $filterGrouping }}">
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary rounded-pill flex-grow-1">
                <i class="bi bi-funnel me-1"></i>Filter
            </button>
            @if($filterKeyword || $filterRoleId || $filterStatus)
                <a href="{{ route('admin.tarif.index', ['group' => $filterGrouping]) }}"
                   class="btn btn-sm btn-outline-secondary rounded-pill">Reset</a>
            @endif
        </div>
    </div>
</form>

{{-- Toggle: Daftar Linear vs Per-Layanan (banding harga) --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="toggle-tab">
        <a href="{{ route('admin.tarif.index', array_merge(request()->query(), ['group' => 'list'])) }}"
           class="{{ $filterGrouping === 'list' ? 'active' : '' }}">
            <i class="bi bi-list-ul me-1"></i>Daftar
        </a>
        <a href="{{ route('admin.tarif.index', array_merge(request()->query(), ['group' => 'per-layanan'])) }}"
           class="{{ $filterGrouping === 'per-layanan' ? 'active' : '' }}">
            <i class="bi bi-bar-chart me-1"></i>Banding per Layanan
        </a>
    </div>
    <small class="text-muted">
        Menampilkan {{ $tarifs->count() }} tarif dari {{ $stats['total_mitra'] }} mitra
    </small>
</div>

{{-- Konten utama --}}
@if($tarifs->isEmpty())
    <div class="zasha-card" style="padding:40px; text-align:center;">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
        <h6 class="mt-3 fw-bold">Tidak Ada Tarif</h6>
        <div class="text-muted small">
            @if($filterKeyword || $filterRoleId || $filterStatus)
                Coba reset filter atau ubah kriteria pencarian.
            @else
                Belum ada mitra yang menambahkan tarif. Mitra bisa add dari /mitra/tarif.
            @endif
        </div>
    </div>
@elseif($filterGrouping === 'per-layanan')
    {{-- Mode Banding per Layanan --}}
    @foreach($grouped as $group)
        <div class="grouped-card">
            <div class="head d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <div class="fw-bold" style="color:#1e293b;">{{ $group->keterangan }}</div>
                    <small class="text-muted">{{ $group->jumlah }} mitra menawarkan layanan ini</small>
                </div>
                @if($group->jumlah > 1)
                    <span class="price-range">
                        <i class="bi bi-graph-up"></i>
                        Rp {{ number_format($group->min_nominal, 0, ',', '.') }} – {{ number_format($group->max_nominal, 0, ',', '.') }}
                    </span>
                @else
                    <span class="text-muted small">
                        Rp {{ number_format($group->min_nominal, 0, ',', '.') }}
                    </span>
                @endif
            </div>
            <div>
                @foreach($group->items as $t)
                    <div class="tarif-row">
                        <div style="width:36px;height:36px;border-radius:10px;
                                    background:{{ ($t->mitra->role->icon_color ?? '#005aa9') . '15' }};
                                    color:{{ $t->mitra->role->icon_color ?? '#005aa9' }};
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi {{ $t->mitra->role->icon ?? 'bi-person-fill' }}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold small text-truncate" style="color:#1e293b;">
                                {{ $t->mitra->nama_panggilan ?? '—' }}
                            </div>
                            <small class="text-muted">
                                {{ $t->mitra->role->name ?? 'Tanpa role' }} · per {{ $t->satuan }}
                                @if(!$t->is_aktif)
                                    <span class="badge bg-secondary ms-1" style="font-size:.6rem;">non-aktif</span>
                                @endif
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color:#1e293b;font-size:.95rem;">
                                Rp {{ number_format($t->nominal, 0, ',', '.') }}
                            </div>
                            <small class="text-muted" style="font-size:.65rem;">
                                pelanggan: Rp {{ number_format($t->hargaPelanggan(), 0, ',', '.') }}
                            </small>
                        </div>
                        <a href="{{ route('admin.mitra.edit', $t->mitra_id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@else
    {{-- Mode Daftar Linear --}}
    <div class="zasha-card" style="padding:0;overflow:hidden;">
        @foreach($tarifs as $t)
            <div class="tarif-row {{ !$t->is_aktif ? 'opacity-75' : '' }}">
                <div style="width:36px;height:36px;border-radius:10px;
                            background:{{ ($t->mitra->role->icon_color ?? '#005aa9') . '15' }};
                            color:{{ $t->mitra->role->icon_color ?? '#005aa9' }};
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi {{ $t->mitra->role->icon ?? 'bi-person-fill' }}"></i>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="fw-semibold" style="color:#1e293b;">{{ $t->keterangan }}</span>
                        @if($t->is_aktif)
                            <span class="badge bg-success" style="font-size:.6rem;">aktif</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:.6rem;">non-aktif</span>
                        @endif
                    </div>
                    <small class="text-muted">
                        {{ $t->mitra->nama_panggilan ?? '—' }}
                        @if($t->mitra->role)
                            · <span style="color:{{ $t->mitra->role->icon_color ?? '#005aa9' }}">{{ $t->mitra->role->name }}</span>
                        @endif
                        · per {{ $t->satuan }}
                    </small>
                </div>
                <div class="text-end" style="min-width:140px;">
                    <div class="fw-bold" style="color:#1e293b;">
                        Rp {{ number_format($t->nominal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size:.65rem;">
                        → pelanggan Rp {{ number_format($t->hargaPelanggan(), 0, ',', '.') }}
                    </small>
                </div>
                <a href="{{ route('admin.mitra.edit', $t->mitra_id) }}"
                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-pencil"></i>
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection
