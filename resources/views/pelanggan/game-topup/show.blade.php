@extends('layouts.pelanggan')
@section('title', 'Top-Up ' . $kategori->nama)

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <a href="{{ route('pelanggan.game-topup.landing') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Daftar Game</a>

    @foreach(['success','error','warning'] as $t)
        @if(session($t)) <div class="alert alert-{{$t}} alert-dismissible fade show">{{ session($t) }}<button class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-3 overflow-hidden">
        @if($kategori->thumbnail)
            <img src="{{ $kategori->thumbnail }}" style="height: 160px; object-fit: cover;" onerror="this.style.display='none'">
        @endif
        <div class="card-body p-4">
            <h4 class="fw-bold mb-1">{{ $kategori->nama }}</h4>
            @if($kategori->sub_nama) <div class="text-muted">{{ $kategori->sub_nama }}</div> @endif
        </div>
    </div>

    @if($kategori->layanans->isEmpty())
        <div class="text-center py-5 text-muted">Belum ada produk untuk kategori ini.</div>
    @else
    <form action="{{ route('pelanggan.game-topup.checkout') }}" method="POST" id="topupForm">@csrf

        {{-- Input UID --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">1. Masukkan ID Akun Game</h6>
                <div class="row g-2">
                    <div class="col-{{ $kategori->butuhServerId() ? 8 : 12 }}">
                        <label class="form-label small fw-semibold">User ID</label>
                        <input type="text" name="user_id_game" class="form-control" required placeholder="Contoh: 123456789">
                    </div>
                    @if($kategori->butuhServerId())
                    <div class="col-4">
                        <label class="form-label small fw-semibold">Zone / Server ID</label>
                        <input type="text" name="server_id" class="form-control" required placeholder="2001">
                    </div>
                    @endif
                </div>
                <div class="form-text small mt-2"><i class="fas fa-info-circle me-1"></i>Pastikan ID benar — produk yang sudah masuk tidak bisa dibatalkan.</div>
            </div>
        </div>

        {{-- Pilih Nominal --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">2. Pilih Nominal</h6>
                <div class="row g-2">
                    @foreach($kategori->layanans as $l)
                    <div class="col-6 col-md-4">
                        <input type="radio" class="btn-check" name="layanan_id" id="l{{ $l->id }}" value="{{ $l->id }}" required>
                        <label class="btn btn-outline-warning w-100 text-start p-3 nominal-btn" for="l{{ $l->id }}">
                            <div class="fw-semibold small">{{ $l->layanan }}</div>
                            <div class="fw-bold text-warning">Rp {{ number_format($l->harga, 0, ',', '.') }}</div>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-warning w-100 fw-bold py-3">
            <i class="fas fa-bolt me-1"></i> Beli & Bayar Sekarang
        </button>
        <div class="text-center small text-muted mt-2">
            <i class="fas fa-wallet me-1"></i>Pembayaran otomatis dipotong dari saldo dompet Anda.
        </div>
    </form>
    @endif
</div>

<style>
.nominal-btn { transition: all 0.2s; }
.btn-check:checked + .nominal-btn { background: #f0a500; color: white; }
.btn-check:checked + .nominal-btn .text-warning { color: white !important; }
</style>
@endsection
