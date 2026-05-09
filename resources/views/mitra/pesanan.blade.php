@extends('layouts.mitra')

@section('content')
<div class="container py-3">
    <div class="d-flex align-items-center mb-4 pt-2">
        <h5 class="fw-bold mb-0">Daftar Pesanan</h5>
    </div>

    @if($pesanan->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
            <p class="small">Belum ada pesanan masuk.</p>
        </div>
    @else
        @foreach($pesanan as $p)
        <div class="card card-custom p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-bold small allow-select">#{{ $p->id_pesanan }}</span>
                <span class="badge rounded-pill
                    @if(in_array($p->status_pesanan, ['selesai'])) bg-success
                    @elseif(in_array($p->status_pesanan, ['dibatalkan'])) bg-danger
                    @else bg-warning text-dark @endif"
                    style="font-size:0.65rem;">
                    {{ ucfirst($p->status_pesanan) }}
                </span>
            </div>
            <div class="small text-muted">
                <i class="bi bi-calendar3 me-1"></i>
                {{ \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i') }}
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="fw-bold text-success small">
                    Rp {{ number_format($p->biaya_jasa ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
        @endforeach
    @endif
</div>
@endsection
