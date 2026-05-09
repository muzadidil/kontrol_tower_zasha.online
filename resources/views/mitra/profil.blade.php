@extends('layouts.mitra')

@section('content')
<div class="container py-3">

    <div style="background:linear-gradient(135deg,#0a5c36,#1a7a4a);padding:40px 20px 60px;text-align:center;margin:-16px -12px 0;">
        <h5 class="fw-bold m-0 text-white">Profil Mitra</h5>
    </div>

    <div class="text-center mt-n4 mb-3" style="margin-top:-40px;">
        <div style="width:80px;height:80px;border-radius:50%;background:#e8f5e9;border:4px solid white;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 4px 15px rgba(0,0,0,0.1);">
            <i class="bi bi-person-fill" style="font-size:2rem;color:#0a5c36;"></i>
        </div>
    </div>

    <div class="text-center mb-4">
        <h5 class="fw-bold mb-1 allow-select">{{ $mitra->nama_panggilan }}</h5>
        <span class="allow-select" style="background:#e8f5e9;color:#0a5c36;padding:3px 12px;border-radius:50px;font-size:0.7rem;font-weight:800;">{{ $mitra->id }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success small rounded-3 py-2">{{ session('success') }}</div>
    @endif

    {{-- Info Akun --}}
    <div class="card card-custom p-4 mb-3">
        <h6 class="fw-bold small text-muted text-uppercase mb-3">Informasi Akun</h6>
        <div class="row g-2">
            <div class="col-12">
                <div class="small text-muted fw-bold">Nomor WhatsApp</div>
                <div class="allow-select">+{{ $mitra->nomor_wa }}</div>
            </div>
            <div class="col-6">
                <div class="small text-muted fw-bold mt-2">Kategori</div>
                <div>{{ $mitra->kategori }}</div>
            </div>
            <div class="col-6">
                <div class="small text-muted fw-bold mt-2">Status</div>
                <span class="{{ $mitra->status === 'Aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
                    {{ $mitra->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Tarif --}}
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold small text-muted text-uppercase mb-3">Tarif Layanan</h6>
        <div class="row g-2">
            <div class="col-6">
                <div class="small text-muted fw-bold">Per Jam</div>
                <div class="fw-bold text-success small">Rp {{ number_format($mitra->tarif_per_jam ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="col-6">
                <div class="small text-muted fw-bold">Per Hari</div>
                <div class="fw-bold text-success small">Rp {{ number_format($mitra->tarif_per_hari ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Logout --}}
    <form action="{{ route('mitra.logout') }}" method="POST" class="mb-4">
        @csrf
        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-bold">
            <i class="bi bi-box-arrow-right me-2"></i>Keluar
        </button>
    </form>

</div>
@endsection
