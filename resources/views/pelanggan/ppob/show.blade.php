@extends('layouts.pelanggan')
@section('title', 'Transaksi PPOB')
@section('content')
<div class="container py-4" style="max-width: 540px;">
    <a href="{{ route('pelanggan.ppob.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Riwayat</a>
    <div class="card border-0 shadow-sm"><div class="card-body p-4">
        @php $bm = ['pending'=>'warning','sukses'=>'success','gagal'=>'danger']; @endphp
        <div class="text-center mb-3">
            <span class="badge bg-{{ $bm[$trx->status] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucfirst($trx->status) }}</span>
        </div>
        <h5 class="fw-bold text-center">{{ $trx->nama_produk }}</h5>
        <div class="text-center text-muted small mb-3">{{ $trx->nomor_tujuan }}</div>
        <hr>
        <div class="d-flex justify-content-between small mb-1"><span>Ref ID</span><span>{{ $trx->digiflazz_ref }}</span></div>
        <div class="d-flex justify-content-between small mb-1"><span>Waktu</span><span>{{ $trx->created_at->format('d M Y H:i:s') }}</span></div>
        @if($trx->sn) <div class="d-flex justify-content-between small mb-1"><span>SN</span><span class="fw-bold text-success">{{ $trx->sn }}</span></div> @endif
        <hr>
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>Rp {{ number_format($trx->harga_jual, 0, ',', '.') }}</span></div>
    </div></div>
</div>
@endsection
