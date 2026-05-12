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
    .btn-status-action.warning { background: #f59e0b; color: white; }
    .btn-status-action.danger  { background: #ef4444; color: white; }
    .btn-status-action.gray    { background: var(--zasha-gray-500); color: white; }
    .btn-status-action:disabled {
        opacity: 0.35; cursor: not-allowed; transform: none;
    }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-archive-fill"></i> Arsip Pesanan</h4>
        <div class="zasha-page-subtitle">Data transaksi yang telah selesai atau dibatalkan.</div>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-light rounded-pill px-3 small fw-bold border">
        <i class="bi bi-arrow-left me-1"></i> Pesanan Aktif
    </a>
</div>

@if(session('pesan'))
    <div class="alert alert-info rounded-3 small">{{ session('pesan') }}</div>
@endif

<div class="zasha-card">
    {{-- Filter Bar --}}
    <form action="{{ route('admin.orders.arsip') }}" method="GET" class="zasha-filter-bar">
        <div class="position-relative flex-grow-1" style="min-width:200px;">
            <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.8rem;"></i>
            <input type="text" name="search" class="form-control ps-5" placeholder="Cari ID, nama, mitra..." value="{{ $search }}">
        </div>
        <select name="status" class="form-select" style="max-width:150px;">
            <option value="">Semua Arsip</option>
            <option value="Selesai" {{ $filter_status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="Batal" {{ $filter_status == 'Batal' ? 'selected' : '' }}>Batal</option>
        </select>
        <input type="text" name="nama" class="form-control" placeholder="Nama pelanggan..." value="{{ $filter_nama }}" style="max-width:180px;">
        <button type="submit" class="btn btn-primary" style="background:var(--zasha-blue);border-color:var(--zasha-blue);">
            <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>
        <a href="{{ route('admin.orders.arsip') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="zasha-list-header">
        <h6><i class="bi bi-clock-history"></i> Riwayat Pesanan</h6>
        <span class="badge-count">{{ $riwayat->count() }}</span>
    </div>

    @forelse($riwayat as $row)
        @php
            $is_batal = ($row->status_pesanan == 'Batal');
            $display_harga = $row->biaya_jasa > 0 ? $row->biaya_jasa : ($row->total_pesanan + $row->ongkir + $row->kode_unik);
        @endphp
        <div class="zasha-row">
            <div class="zasha-row-icon {{ $is_batal ? 'danger' : 'success' }}">
                <i class="bi {{ $is_batal ? 'bi-x-circle-fill' : 'bi-check-circle-fill' }}"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    <span class="text-muted small fw-normal">#{{ $row->id_pesanan }}</span> · {{ $row->nama_pelanggan }}
                </div>
                <div class="zasha-row-meta">
                    <span class="zasha-status-badge {{ $is_batal ? 'danger' : 'success' }}">
                        {{ $row->status_pesanan }}
                    </span>
                    <span><i class="bi bi-person-badge"></i> {{ $row->nama_mitra ?: 'N/A' }}</span>
                    <span><i class="bi bi-credit-card"></i> {{ $row->metode_pembayaran ?? '-' }}</span>
                    <span><i class="bi bi-clock"></i> {{ date('d M Y, H:i', strtotime($row->tanggal_pesanan)) }}</span>
                </div>
            </div>
            <div class="zasha-row-amount">
                <div class="zasha-row-amount-main">Rp {{ number_format($display_harga, 0, ',', '.') }}</div>
                <div class="zasha-row-amount-sub">Koreksi status →</div>
            </div>
            {{-- Tombol koreksi status (sebaris, kompak) --}}
            <div class="d-flex gap-1 flex-shrink-0">
                @foreach([
                    ['Selesai', 'success', 'bi-check-lg'],
                    ['Batal',   'danger',  'bi-x-lg'],
                    ['Lunas',   'warning', 'bi-cash'],
                    ['Pending', 'gray',    'bi-hourglass'],
                ] as $action)
                    @php [$label, $color, $icon] = $action; @endphp
                    <form action="{{ route('admin.orders.arsip.update') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="id_pesanan" value="{{ $row->id_pesanan }}">
                        <input type="hidden" name="status_baru" value="{{ $label }}">
                        <button type="submit" class="btn-status-action {{ $color }}"
                                {{ $row->status_pesanan === $label ? 'disabled' : '' }}
                                onclick="return confirm('Ubah status #{{ $row->id_pesanan }} ke {{ $label }}?')"
                                title="Koreksi ke: {{ $label }}">
                            <i class="bi {{ $icon }}"></i> {{ $label }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    @empty
        <div class="zasha-empty">
            <i class="bi bi-clipboard-x"></i>
            <div class="zasha-empty-title">Tidak ada data arsip</div>
            <div class="zasha-empty-sub">Pesanan yang sudah selesai/dibatalkan akan muncul di sini.</div>
        </div>
    @endforelse
</div>
@endsection
