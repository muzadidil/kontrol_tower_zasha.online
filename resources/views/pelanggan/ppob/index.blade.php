@extends('layouts.pelanggan')
@section('title', 'PPOB')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">PPOB / Pulsa & Token</h4>
        <a href="{{ route('pelanggan.ppob.form') }}" class="btn btn-warning fw-semibold"><i class="fas fa-plus me-1"></i> Beli Baru</a>
    </div>

    @foreach(['success','error','warning'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach

    <h6 class="text-muted small mb-3">Riwayat Transaksi</h6>
    @forelse($trxs as $trx)
    <div class="card border-0 shadow-sm mb-2"><div class="card-body p-3">
        <div class="d-flex justify-content-between">
            <div>
                <span class="badge bg-{{ $trx->jenis_produk === 'pulsa' ? 'info' : ($trx->jenis_produk === 'paket_data' ? 'primary' : 'warning') }}">{{ ucwords(str_replace('_', ' ', $trx->jenis_produk)) }}</span>
                <span class="ms-2 fw-semibold small">{{ $trx->nama_produk }}</span>
                <div class="small text-muted">{{ $trx->nomor_tujuan }} · {{ $trx->created_at->format('d M Y H:i') }}</div>
                @if($trx->sn) <div class="small text-success">SN: {{ $trx->sn }}</div> @endif
            </div>
            <div class="text-end">
                @php $bm = ['pending'=>'warning','sukses'=>'success','gagal'=>'danger']; @endphp
                <span class="badge bg-{{ $bm[$trx->status] ?? 'secondary' }}">{{ ucfirst($trx->status) }}</span>
                <div class="fw-bold small">Rp {{ number_format($trx->harga_jual, 0, ',', '.') }}</div>
            </div>
        </div>
    </div></div>
    @empty
    <div class="text-center py-5 text-muted">Belum ada transaksi.</div>
    @endforelse

    {{ $trxs->links() }}
</div>
@endsection
