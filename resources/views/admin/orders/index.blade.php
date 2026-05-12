@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')

<style>
    .btn-status-action {
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 0.7rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.15s;
        opacity: 0.8;
    }
    .btn-status-action:hover { opacity: 1; transform: translateY(-1px); }
    .btn-status-action.success { background: #10b981; color: white; }
    .btn-status-action.process { background: #3b82f6; color: white; }
    .btn-status-action.warning { background: #f59e0b; color: white; }
    .btn-status-action.danger  { background: #ef4444; color: white; }
    .btn-status-action:disabled {
        opacity: 0.35; cursor: not-allowed; transform: none;
    }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-receipt-cutoff"></i> Pesanan Aktif</h4>
        <div class="zasha-page-subtitle">
            Pesanan yang sedang berjalan. Untuk pesanan Selesai/Batal lihat
            <a href="{{ route('admin.orders.arsip') }}" class="text-decoration-none fw-bold">Arsip Pesanan</a>.
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.orders.arsip') }}" class="btn btn-light rounded-pill px-3 small fw-bold border">
            <i class="bi bi-archive me-1"></i> Arsip
        </a>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-light rounded-pill px-3 border" title="Refresh">
            <i class="bi bi-arrow-clockwise"></i>
        </a>
    </div>
</div>

@if(session('pesan'))
    <div class="alert alert-info rounded-3 small">{{ session('pesan') }}</div>
@endif

<div class="zasha-card">
    {{-- Filter Bar --}}
    <form action="{{ route('admin.orders.index') }}" method="GET" class="zasha-filter-bar">
        <div class="position-relative flex-grow-1" style="min-width:200px;">
            <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.8rem;"></i>
            <input type="text" name="search" class="form-control ps-5" placeholder="Cari ID, pelanggan, atau mitra..." value="{{ $search }}">
        </div>
        <select name="status" class="form-select" style="max-width:170px;">
            <option value="">Semua Status</option>
            @foreach(['Pending', 'Proses', 'Lunas', 'Menunggu Konfirmasi'] as $st)
                <option value="{{ $st }}" {{ $filter_status == $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary" style="background:var(--zasha-blue);border-color:var(--zasha-blue);">
            <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-light">Reset</a>
    </form>

    {{-- List Header --}}
    <div class="zasha-list-header">
        <h6><i class="bi bi-list-ul"></i> Daftar Pesanan</h6>
        <span class="badge-count">{{ count($orders) }}</span>
    </div>

    @forelse($orders as $row)
        @php
            $st = $row->status;
            $tipe = $row->tipe_order;
            $statusClass = match(strtolower($st)) {
                'pending', 'menunggu_mitra' => 'pending',
                'proses', 'belanja', 'menuju_pengantaran', 'diantar', 'dikerjakan' => 'process',
                'lunas', 'selesai' => 'success',
                'menunggu konfirmasi', 'menunggu_konfirmasi' => 'purple',
                default => 'gray',
            };
            $iconClass = $tipe == 'JASA' ? '' : 'purple';
            $iconBi = $tipe == 'JASA' ? 'bi-briefcase-fill' : 'bi-bag-fill';
        @endphp
        <div class="zasha-row">
            <div class="zasha-row-icon {{ $iconClass }}">
                <i class="bi {{ $iconBi }}"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    <span class="text-muted small fw-normal">#{{ $row->id }}</span> · {{ $row->nama_pelanggan }}
                </div>
                <div class="zasha-row-meta">
                    <span class="zasha-status-badge {{ $statusClass }}">{{ $st }}</span>
                    <span><i class="bi bi-tag-fill"></i> {{ $tipe }}</span>
                    <span><i class="bi bi-person-badge"></i> {{ $row->nama_pekerja }}</span>
                    <span><i class="bi bi-credit-card"></i> {{ $row->metode }}</span>
                    <span class="d-none d-md-inline">
                        <i class="bi bi-clock"></i> {{ date('d M, H:i', strtotime($row->tgl)) }}
                    </span>
                </div>
            </div>
            <div class="zasha-row-amount">
                <div class="zasha-row-amount-main">Rp {{ number_format($row->total_biaya, 0, ',', '.') }}</div>
            </div>
            {{-- Tombol aksi: Proses / Lunas / Selesai / Batal (sebaris, kompak) --}}
            <div class="d-flex gap-1 flex-shrink-0">
                @foreach([
                    ['Proses',  'process',  'bi-arrow-clockwise'],
                    ['Lunas',   'success',  'bi-cash'],
                    ['Selesai', 'success',  'bi-check-lg'],
                    ['Batal',   'danger',   'bi-x-lg'],
                ] as $action)
                    @php [$label, $color, $icon] = $action; @endphp
                    <form action="{{ route('admin.orders.updateStatus') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="id_pesanan" value="{{ $row->id }}">
                        <input type="hidden" name="tipe_order" value="{{ $tipe }}">
                        <input type="hidden" name="status_baru" value="{{ $label }}">
                        <button type="submit" class="btn-status-action {{ $color }}"
                                {{ $st === $label ? 'disabled' : '' }}
                                onclick="return confirm('Ubah status #{{ $row->id }} ke {{ $label }}?')"
                                title="Set: {{ $label }}">
                            <i class="bi {{ $icon }}"></i> {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @empty
        <div class="zasha-empty">
            <i class="bi bi-clipboard-x"></i>
            <div class="zasha-empty-title">Tidak ada pesanan aktif</div>
            <div class="zasha-empty-sub">
                Pesanan Selesai/Batal ada di <a href="{{ route('admin.orders.arsip') }}">Arsip Pesanan</a>.
            </div>
        </div>
    @endforelse
</div>
@endsection
