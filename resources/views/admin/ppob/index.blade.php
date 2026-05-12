@extends('layouts.admin')
@section('title', 'Monitoring PPOB')

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
    .btn-status-action.success:disabled,
    .btn-status-action.warning:disabled,
    .btn-status-action.danger:disabled {
        opacity: 0.35; cursor: not-allowed; transform: none;
    }
    .ppob-stat-card {
        background: white;
        border: 1px solid var(--zasha-gray-200);
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .ppob-stat-label {
        font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px;
        color: var(--zasha-gray-400);
        margin-bottom: 4px;
    }
    .ppob-stat-value {
        font-size: 1.1rem; font-weight: 800;
        color: var(--zasha-gray-900);
    }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-mobile-vibrate"></i> Monitoring PPOB</h4>
        <div class="zasha-page-subtitle">Pulsa, Paket Data, Token Listrik — transaksi via Digiflazz.</div>
    </div>
    <a href="{{ route('admin.ppob.index') }}" class="btn btn-light rounded-pill px-3 border small fw-bold">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
    </a>
</div>

@if(session('notif'))
    <div class="alert alert-success rounded-3 small">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('notif') }}
    </div>
@endif

{{-- ── STATS ROW ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="ppob-stat-card">
            <div class="ppob-stat-label">Total Transaksi</div>
            <div class="ppob-stat-value">{{ number_format($stats['total_trx'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="ppob-stat-card">
            <div class="ppob-stat-label">Total Omzet</div>
            <div class="ppob-stat-value" style="color:var(--zasha-blue);">Rp {{ number_format($stats['total_omzet'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="ppob-stat-card">
            <div class="ppob-stat-label">Profit Zasha</div>
            <div class="ppob-stat-value" style="color:var(--zasha-success);">Rp {{ number_format($stats['total_margin'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="ppob-stat-card">
            <div class="ppob-stat-label">Status</div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:6px;">
                <span class="zasha-status-badge success">Sukses {{ $stats['sukses'] }}</span>
                <span class="zasha-status-badge pending">Pending {{ $stats['pending'] }}</span>
                <span class="zasha-status-badge danger">Gagal {{ $stats['gagal'] }}</span>
            </div>
        </div>
    </div>
</div>

<div class="zasha-card">
    {{-- Filter Bar --}}
    <form method="GET" class="zasha-filter-bar">
        <div class="position-relative flex-grow-1" style="min-width:180px;">
            <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.8rem;"></i>
            <input type="text" name="search" class="form-control ps-5" placeholder="Ref ID / Nomor / Produk..." value="{{ request('search') }}">
        </div>
        <select name="status" class="form-select" style="max-width:130px;">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Pending</option>
            <option value="sukses" {{ request('status')==='sukses'?'selected':'' }}>Sukses</option>
            <option value="gagal" {{ request('status')==='gagal'?'selected':'' }}>Gagal</option>
        </select>
        <select name="jenis" class="form-select" style="max-width:140px;">
            <option value="">Semua Jenis</option>
            <option value="pulsa" {{ request('jenis')==='pulsa'?'selected':'' }}>Pulsa</option>
            <option value="paket_data" {{ request('jenis')==='paket_data'?'selected':'' }}>Paket Data</option>
            <option value="token_listrik" {{ request('jenis')==='token_listrik'?'selected':'' }}>Token Listrik</option>
        </select>
        <select name="user_type" class="form-select" style="max-width:140px;">
            <option value="">Semua Pembeli</option>
            <option value="pelanggan" {{ request('user_type')==='pelanggan'?'selected':'' }}>Pelanggan</option>
            <option value="mitra" {{ request('user_type')==='mitra'?'selected':'' }}>Mitra</option>
        </select>
        <button class="btn btn-primary" style="background:var(--zasha-blue); border-color:var(--zasha-blue);">
            <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>
        <a href="{{ route('admin.ppob.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="zasha-list-header">
        <h6><i class="bi bi-list-ul"></i> Daftar Transaksi PPOB</h6>
        <span class="badge-count">{{ $trxs->total() }}</span>
    </div>

    @forelse($trxs as $trx)
        @php
            $statusMap = [
                'pending' => ['pending', 'bi-hourglass-split', 'warning'],
                'sukses'  => ['success', 'bi-check-circle-fill', 'success'],
                'gagal'   => ['danger',  'bi-x-circle-fill', 'danger'],
            ];
            [$badgeClass, $iconBi, $iconColor] = $statusMap[$trx->status] ?? ['gray', 'bi-question-circle', 'gray'];

            $jenisIcon = match($trx->jenis_produk) {
                'pulsa' => 'bi-phone',
                'paket_data' => 'bi-wifi',
                'token_listrik' => 'bi-lightning-charge-fill',
                default => 'bi-box',
            };
        @endphp
        <div class="zasha-row">
            <div class="zasha-row-icon {{ $iconColor }}">
                <i class="bi {{ $jenisIcon }}"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    <span class="text-muted small fw-normal">{{ $trx->digiflazz_ref ?? '#'.$trx->id }}</span>
                    · {{ $trx->nama_produk }}
                </div>
                <div class="zasha-row-meta">
                    <span class="zasha-status-badge {{ $badgeClass }}">
                        <i class="bi {{ $iconBi }}"></i> {{ ucfirst($trx->status) }}
                    </span>
                    <span><i class="bi bi-person-circle"></i> {{ ucfirst($trx->user_type) }} #{{ $trx->user_id }}</span>
                    <span class="font-monospace"><i class="bi bi-telephone"></i> {{ $trx->nomor_tujuan }}</span>
                    <span><i class="bi bi-clock"></i> {{ $trx->created_at->format('d M, H:i') }}</span>
                </div>
            </div>
            <div class="zasha-row-amount">
                <div class="zasha-row-amount-main">Rp {{ number_format($trx->harga_jual, 0, ',', '.') }}</div>
                <div class="zasha-row-amount-sub">+Rp {{ number_format($trx->margin_zasha, 0, ',', '.') }} margin</div>
            </div>
            {{-- Tombol aksi: Sukses / Pending / Gagal (sebaris, kompak) --}}
            <div class="d-flex gap-1 flex-shrink-0">
                <form action="{{ route('admin.ppob.updateStatus', $trx->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="sukses">
                    <button type="submit" class="btn-status-action success"
                            {{ $trx->status === 'sukses' ? 'disabled' : '' }}
                            onclick="return confirm('Ubah status #{{ $trx->id }} ke SUKSES?')"
                            title="Set status: Sukses">
                        <i class="bi bi-check-lg"></i> Sukses
                    </button>
                </form>
                <form action="{{ route('admin.ppob.updateStatus', $trx->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="pending">
                    <button type="submit" class="btn-status-action warning"
                            {{ $trx->status === 'pending' ? 'disabled' : '' }}
                            onclick="return confirm('Ubah status #{{ $trx->id }} ke PENDING?')"
                            title="Set status: Pending">
                        <i class="bi bi-hourglass"></i> Pending
                    </button>
                </form>
                <form action="{{ route('admin.ppob.updateStatus', $trx->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="gagal">
                    <button type="submit" class="btn-status-action danger"
                            {{ $trx->status === 'gagal' ? 'disabled' : '' }}
                            onclick="return confirm('Ubah status #{{ $trx->id }} ke GAGAL?')"
                            title="Set status: Gagal">
                        <i class="bi bi-x-lg"></i> Gagal
                    </button>
                </form>
                <a href="{{ route('admin.ppob.show', $trx->id) }}" class="btn-status-action"
                   style="background:var(--zasha-gray-100); color:var(--zasha-gray-700);"
                   title="Detail">
                    <i class="bi bi-eye"></i>
                </a>
            </div>
        </div>
    @empty
        <div class="zasha-empty">
            <i class="bi bi-mobile-vibrate"></i>
            <div class="zasha-empty-title">Belum ada transaksi PPOB</div>
            <div class="zasha-empty-sub">Transaksi pulsa, paket data, token listrik akan muncul di sini.</div>
        </div>
    @endforelse

    @if(method_exists($trxs, 'links'))
        <div class="p-3 border-top">{{ $trxs->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
