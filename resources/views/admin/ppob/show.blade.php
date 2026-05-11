@extends('layouts.admin')
@section('title', 'Detail PPOB ' . $trx->digiflazz_ref)

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            @php $bm = ['pending'=>'warning','sukses'=>'success','gagal'=>'danger']; @endphp
            <div class="d-flex justify-content-between mb-3">
                <h5 class="fw-bold mb-0">{{ $trx->nama_produk }}</h5>
                <span class="badge bg-{{ $bm[$trx->status] }} fs-6 px-3 py-2">{{ ucfirst($trx->status) }}</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6"><div class="text-muted small">Ref ID</div><code>{{ $trx->digiflazz_ref }}</code></div>
                <div class="col-md-6"><div class="text-muted small">Pembeli</div><div>{{ ucfirst($trx->user_type) }} #{{ $trx->user_id }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Jenis</div><div>{{ ucwords(str_replace('_', ' ', $trx->jenis_produk)) }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Nomor Tujuan</div><div class="font-monospace">{{ $trx->nomor_tujuan }}</div></div>
                <div class="col-md-6"><div class="text-muted small">Kode Produk</div><div class="font-monospace">{{ $trx->kode_produk }}</div></div>
                @if($trx->sn) <div class="col-md-6"><div class="text-muted small">Serial Number</div><div class="font-monospace text-success">{{ $trx->sn }}</div></div> @endif
            </div>

            <hr class="my-4">

            <h6 class="fw-bold mb-3">Rincian Harga</h6>
            <div class="d-flex justify-content-between small mb-1"><span>Harga Modal</span><span>Rp {{ number_format($trx->harga_modal, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Harga Jual</span><span>Rp {{ number_format($trx->harga_jual, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between fw-bold text-success"><span>Margin Zasha</span><span>Rp {{ number_format($trx->margin_zasha, 0, ',', '.') }}</span></div>

            @if($trx->digiflazz_response)
            <hr class="my-4">
            <h6 class="fw-bold mb-2">Response Digiflazz</h6>
            <pre class="bg-light p-3 rounded small">{{ json_encode($trx->digiflazz_response, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            @endif

            <div class="text-muted small mt-3">
                Dibuat: {{ $trx->created_at->format('d M Y, H:i:s') }} ·
                @if($trx->processed_at) Diproses: {{ $trx->processed_at->format('d M Y, H:i:s') }} @endif
            </div>
        </div>
    </div>
</div>
@endsection
