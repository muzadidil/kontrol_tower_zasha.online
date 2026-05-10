@extends('layouts.pelanggan')
@section('title', 'Detail ' . $order->order_code)
@section('content')
<div class="container py-4" style="max-width: 640px;">
    <a href="{{ route('pelanggan.tenaga.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Daftar Tenaga</a>
    @foreach(['success','error','warning'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <div class="d-flex justify-content-between"><div><div class="text-muted small">Order</div><div class="fw-bold fs-5">{{ $order->order_code }}</div></div>
            <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span></div>
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Mitra</h6>
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-user-tie fa-2x text-warning"></i>
            <div><div class="fw-semibold">{{ $order->mitra->nama_asli ?? '-' }}</div>
            @if($order->mitra->no_wa) <a href="https://wa.me/{{ $order->mitra->no_wa }}" target="_blank" class="text-success small"><i class="fab fa-whatsapp"></i> {{ $order->mitra->no_wa }}</a> @endif</div>
        </div>
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Detail</h6>
        <div class="row g-2">
            <div class="col-6"><div class="text-muted small">Layanan</div><div class="fw-semibold">{{ $order->mitraLayanan->masterLayanan->nama_layanan ?? '-' }}</div></div>
            <div class="col-6"><div class="text-muted small">Durasi</div><div class="fw-semibold">{{ $order->durasi }} {{ $order->tipe_durasi }}</div></div>
            <div class="col-6"><div class="text-muted small">Tipe</div><div class="fw-semibold">{{ ucfirst($order->tipe_waktu) }}</div></div>
            <div class="col-6"><div class="text-muted small">Pembayaran</div><div class="fw-semibold">{{ strtoupper($order->metode_pembayaran) }}</div></div>
        </div>
        @if($order->keterangan_kerja) <div class="mt-3"><div class="text-muted small mb-1">Keterangan</div><div class="bg-light rounded p-2 small">{{ $order->keterangan_kerja }}</div></div> @endif
        <div class="mt-3"><div class="text-muted small mb-1">Alamat</div><div class="small">{{ $order->alamat_pelanggan }}</div></div>
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Rincian</h6>
        <div class="d-flex justify-content-between small mb-1"><span>Tarif × Durasi</span><span>Rp {{ number_format($order->tarif, 0, ',', '.') }} × {{ $order->durasi }}</span></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="text-warning">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</span></div>
    </div></div>

    @if($order->status->value === 'menunggu_konfirmasi')
    <div class="card border-0 shadow-sm border-start border-4 border-warning"><div class="card-body p-4">
        <h6 class="fw-bold">Pekerjaan Selesai?</h6>
        <p class="small text-muted">Mitra menyatakan pekerjaan selesai. Konfirmasi atau buka dispute.</p>
        <div class="d-flex gap-2">
            <form action="{{ route('pelanggan.tenaga.konfirmasi', $order->id) }}" method="POST">@csrf
                <button class="btn btn-success" onclick="return confirm('Konfirmasi selesai?')">Konfirmasi</button>
            </form>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal">Ada Masalah</button>
        </div>
    </div></div>
    @endif
</div>
<div class="modal fade" id="disputeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0">
    <div class="modal-header border-0"><h5 class="fw-bold">Buka Dispute</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('pelanggan.tenaga.dispute', $order->id) }}" method="POST">@csrf
        <div class="modal-body"><textarea name="deskripsi" class="form-control" rows="4" minlength="20" required></textarea></div>
        <div class="modal-footer border-0"><button class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger">Kirim</button></div>
    </form>
</div></div></div>
@endsection
