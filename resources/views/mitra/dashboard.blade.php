@extends('layouts.mitra')

@section('content')
<div class="page-pad stack-4">

    {{-- Header (fib-3 gap) --}}
    <div class="d-flex justify-content-between align-items-center" style="padding-top: var(--fib-2);">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-34" style="background: linear-gradient(135deg,var(--mitra-blue),var(--mitra-blue-dark));display:flex;align-items:center;justify-content:center;box-shadow:0 var(--fib-1) var(--fib-3) rgba(0,90,169,0.25);">
                <i class="bi bi-person-fill text-white" style="font-size:var(--t-md);"></i>
            </div>
            <div>
                <div class="t-xs label-up" style="color:var(--ink-soft);margin-bottom:2px;">Halo,</div>
                <h6 class="fw-bold mb-0 allow-select t-lg" style="line-height:1;">{{ explode(' ', trim($mitra->nama_panggilan ?? $mitra->nama_asli ?? 'Mitra'))[0] }}</h6>
            </div>
        </div>
        <span class="{{ ($mitra->status_mitra ?? '') === 'aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
            {{ ucfirst($mitra->status_mitra ?? 'offline') }}
        </span>
    </div>

    {{-- Hero Saldo (Golden Ratio: padding fib-4, content centered) --}}
    <div class="hero-mitra">
        <div class="hero-content">
            <div class="d-flex justify-content-between align-items-start" style="margin-bottom: var(--fib-3);">
                <div>
                    <div class="label-up" style="color:rgba(255,255,255,0.7);">Saldo Mitra</div>
                    <h2 class="fw-bold mb-0 mt-1 allow-select" style="font-size: var(--t-2xl); letter-spacing: -0.02em;">
                        Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}
                    </h2>
                </div>
                <div style="width: var(--fib-4); height: var(--fib-4); border-radius: 50%; background: rgba(179,218,253,0.4); display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-stars" style="color: var(--mitra-blue-soft); font-size: var(--t-xs);"></i>
                </div>
            </div>

            <div class="t-xxs allow-select" style="color: rgba(255,255,255,0.6); margin-bottom: var(--fib-3);">
                ID: {{ $mitra->id_mitra ?? '-' }}
            </div>

            <div class="d-flex" style="gap: var(--fib-2);">
                <a href="{{ route('mitra.saldo') }}" class="btn-mitra-ghost flex-grow-1 text-center" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--fib-1);">
                    <i class="bi bi-arrow-down-circle"></i>
                    <span>Tarik</span>
                </a>
                <a href="{{ route('mitra.topup.form') }}" class="btn-mitra-ghost flex-grow-1 text-center" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--fib-1);">
                    <i class="bi bi-plus-circle"></i>
                    <span>Topup</span>
                </a>
                <a href="{{ route('mitra.saldo') }}" class="btn-mitra-ghost flex-grow-1 text-center" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--fib-1);">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats (golden 1:1.618 ratio - 2 cards, 3rd one hidden) --}}
    <div class="row g-3">
        <div class="col-6">
            <div class="card-custom" style="padding: var(--fib-3);">
                <div class="d-flex align-items-center" style="gap: var(--fib-2); margin-bottom: var(--fib-2);">
                    <div style="width: var(--fib-3); height: var(--fib-3); border-radius: var(--r-sm); background: var(--mitra-blue-light);"></div>
                    <span class="label-up t-xxs">Total Order</span>
                </div>
                <div class="fw-bold" style="font-size: var(--t-xl); color: var(--ink);">{{ $totalPesanan ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card-custom" style="padding: var(--fib-3);">
                <div class="d-flex align-items-center" style="gap: var(--fib-2); margin-bottom: var(--fib-2);">
                    <div style="width: var(--fib-3); height: var(--fib-3); border-radius: var(--r-sm); background: var(--mitra-blue-soft);"></div>
                    <span class="label-up t-xxs">Aktif</span>
                </div>
                <div class="fw-bold" style="font-size: var(--t-xl); color: var(--mitra-blue);">{{ $pesananAktif ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Menu Tiles (2x2 grid, fibonacci radius) --}}
    <div style="margin-bottom: var(--fib-7);">
        <div class="d-flex justify-content-between align-items-center" style="margin-bottom: var(--fib-3);">
            <h6 class="label-up mb-0">Fitur</h6>
            <span class="t-xs" style="color: var(--ink-soft);">{{ count([1,2,3,4]) }} menu</span>
        </div>
        <div class="row g-3">
            <div class="col-6">
                <a href="{{ route('mitra.pesanan') }}" class="menu-tile">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-soft); color: var(--mitra-blue);">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Pesanan</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Lihat semua</div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('mitra.saldo') }}" class="menu-tile">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-tint); color: var(--mitra-blue-mid);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Saldo</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Tarik & riwayat</div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('mitra.profil') }}" class="menu-tile">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-soft); color: var(--mitra-blue-dark);">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Profil</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Data akun</div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <div class="menu-tile" style="opacity: 0.55; cursor: not-allowed;">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-tint); color: var(--mitra-blue-mid);">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Statistik</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Segera hadir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
