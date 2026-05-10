@extends('layouts.pelanggan')
@section('title', 'Top-Up Game & Voucher')

@section('content')
<div class="container py-4" style="max-width: 920px;">
    <h4 class="fw-bold mb-1"><i class="fas fa-gamepad text-warning me-2"></i>Top-Up Game & Voucher</h4>
    <p class="text-muted small mb-4">Pilih game atau voucher kesayangan, isi UID, langsung diproses.</p>

    @forelse($kategoris as $tipe => $list)
    <h6 class="fw-bold text-uppercase text-muted small mb-3">{{ ucfirst($tipe) }}</h6>
    <div class="row g-3 mb-4">
        @foreach($list as $k)
        <div class="col-6 col-md-3">
            <a href="{{ route('pelanggan.game-topup.show', $k->kode) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 katalog-card">
                    @if($k->thumbnail)
                        <img src="{{ $k->thumbnail }}" class="card-img-top" style="height: 100px; object-fit: cover;" onerror="this.style.display='none'">
                    @else
                        <div class="bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 100px;">
                            <i class="fas fa-{{ $tipe === 'game' ? 'gamepad' : ($tipe === 'voucher' ? 'ticket-alt' : 'mobile-alt') }} fa-3x text-warning"></i>
                        </div>
                    @endif
                    <div class="card-body p-3 text-center">
                        <div class="fw-bold small text-dark">{{ $k->nama }}</div>
                        @if($k->sub_nama) <div class="text-muted" style="font-size: 11px;">{{ $k->sub_nama }}</div> @endif
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    @empty
    <div class="text-center py-5 text-muted">
        <i class="fas fa-inbox fa-3x mb-3"></i>
        <p>Belum ada produk tersedia.</p>
    </div>
    @endforelse
</div>

<style>
.katalog-card { transition: all 0.2s; }
.katalog-card:hover { transform: translateY(-3px); box-shadow: 0 8px 16px rgba(240,165,0,0.2) !important; }
</style>
@endsection
