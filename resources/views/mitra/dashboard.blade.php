@extends('layouts.mitra')

@section('content')
<div class="container py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
        <div class="d-flex align-items-center">
            <div style="width:45px;height:45px;border-radius:14px;background:linear-gradient(135deg,#0a5c36,#1a7a4a);display:flex;align-items:center;justify-content:center;" class="me-3 shadow-sm">
                <i class="bi bi-person-fill text-white fs-5"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 allow-select">Halo, {{ explode(' ', trim($mitra->nama_panggilan))[0] }}!</h6>
                <span style="background:#e8f5e9;color:#0a5c36;padding:3px 10px;border-radius:50px;font-weight:800;font-size:0.65rem;" class="allow-select">{{ $mitra->id_mitra }}</span>
            </div>
        </div>
        <span class="{{ $mitra->status_mitra === 'aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
            {{ ucfirst($mitra->status_mitra) }}
        </span>
    </div>

    {{-- Saldo --}}
    <div class="card card-custom p-3 mb-4 border-0" style="background:linear-gradient(135deg,#0a5c36,#1a7a4a);">
        <small class="text-white-50 fw-bold d-block mb-1" style="font-size:10px;">SALDO MITRA</small>
        <h4 class="fw-bold text-white mb-0 allow-select">
            Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}
        </h4>
        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('mitra.saldo') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold" style="font-size:0.7rem;">
                <i class="bi bi-arrow-down-circle me-1"></i>Tarik Dana
            </a>
            <a href="{{ route('mitra.saldo') }}" class="btn btn-sm rounded-pill px-3 fw-bold text-white" style="background:rgba(255,255,255,0.2);font-size:0.7rem;">
                <i class="bi bi-clock-history me-1"></i>Riwayat
            </a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="card card-custom p-3 text-center">
                <div class="fw-bold text-muted" style="font-size:0.65rem;text-transform:uppercase;">Total Pesanan</div>
                <div class="fw-bold text-dark mt-1" style="font-size:1.5rem;">{{ $totalPesanan }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card card-custom p-3 text-center">
                <div class="fw-bold text-muted" style="font-size:0.65rem;text-transform:uppercase;">Pesanan Aktif</div>
                <div class="fw-bold mt-1" style="font-size:1.5rem;color:#0a5c36;">{{ $pesananAktif }}</div>
            </div>
        </div>
    </div>

    {{-- Menu Cepat --}}
    <h6 class="fw-bold mb-3 small text-muted text-uppercase" style="letter-spacing:0.5px;">Fitur</h6>
    <div class="row g-3">
        <div class="col-6">
            <a href="{{ route('mitra.pesanan') }}" class="card card-custom p-3 d-flex flex-row align-items-center text-decoration-none gap-3">
                <div style="width:42px;height:42px;border-radius:12px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-clipboard-check text-success fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold small text-dark">Pesanan</div>
                    <div class="text-muted" style="font-size:0.65rem;">Lihat semua</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('mitra.saldo') }}" class="card card-custom p-3 d-flex flex-row align-items-center text-decoration-none gap-3">
                <div style="width:42px;height:42px;border-radius:12px;background:#fef9c3;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-wallet2 text-warning fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold small text-dark">Saldo</div>
                    <div class="text-muted" style="font-size:0.65rem;">Tarik & riwayat</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('mitra.profil') }}" class="card card-custom p-3 d-flex flex-row align-items-center text-decoration-none gap-3">
                <div style="width:42px;height:42px;border-radius:12px;background:#ede9fe;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-person-circle text-purple fs-5" style="color:#7c3aed;"></i>
                </div>
                <div>
                    <div class="fw-bold small text-dark">Profil</div>
                    <div class="text-muted" style="font-size:0.65rem;">Data akun</div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <div class="card card-custom p-3 d-flex flex-row align-items-center gap-3" style="opacity:0.5;">
                <div style="width:42px;height:42px;border-radius:12px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-graph-up text-secondary fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold small text-dark">Statistik</div>
                    <div class="text-muted" style="font-size:0.65rem;">Segera hadir</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
