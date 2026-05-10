@extends('layouts.mitra')
@section('title', 'Detail ' . $order->order_code)
@section('content')
<div class="container py-4" style="max-width: 640px;">
    <a href="{{ route('mitra.tenaga.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    @foreach(['success','error','info','warning'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <div class="d-flex justify-content-between"><div><div class="fw-bold fs-5">{{ $order->order_code }}</div></div>
            <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span></div>
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Pelanggan</h6>
        <div class="fw-semibold">{{ $order->pelanggan->nama_pelanggan ?? '-' }}</div>
        @if($order->pelanggan->no_wa) <a href="https://wa.me/{{ $order->pelanggan->no_wa }}" target="_blank" class="text-success small"><i class="fab fa-whatsapp"></i> {{ $order->pelanggan->no_wa }}</a> @endif
        <div class="small text-muted mt-2">{{ $order->alamat_pelanggan }}</div>
        @if($order->pelanggan_lat && $order->pelanggan_lng)
            <a href="https://www.google.com/maps?q={{ $order->pelanggan_lat }},{{ $order->pelanggan_lng }}" target="_blank" class="btn btn-outline-warning btn-sm mt-2"><i class="fas fa-map-marker-alt me-1"></i>Maps</a>
        @endif
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Detail Pekerjaan</h6>
        <div class="row g-2">
            <div class="col-6"><div class="text-muted small">Layanan</div><div class="small fw-semibold">{{ $order->mitraLayanan->masterLayanan->nama_layanan ?? '-' }}</div></div>
            <div class="col-6"><div class="text-muted small">Durasi</div><div class="small fw-semibold">{{ $order->durasi }} {{ $order->tipe_durasi }}</div></div>
            <div class="col-6"><div class="text-muted small">Pembayaran</div><div class="small fw-semibold">{{ strtoupper($order->metode_pembayaran) }}</div></div>
            <div class="col-6"><div class="text-muted small">Pendapatan</div><div class="small fw-semibold text-success">Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</div></div>
        </div>
        @if($order->keterangan_kerja) <div class="mt-3 bg-light rounded p-2 small">{{ $order->keterangan_kerja }}</div> @endif
    </div></div>

    {{-- Actions --}}
    @if($order->status->value === 'menunggu_mitra')
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Respons Diperlukan</h6>
        <div class="d-flex gap-2">
            <form action="{{ route('mitra.tenaga.terima', $order->id) }}" method="POST">@csrf<button class="btn btn-success">Terima</button></form>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">Tolak</button>
        </div>
    </div></div>
    @endif

    @if($order->status->value === 'menuju_lokasi')
    <div class="card border-0 shadow-sm border-start border-4 border-info mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Sudah di Lokasi?</h6>
        <form action="{{ route('mitra.tenaga.mulai-kerja', $order->id) }}" method="POST">@csrf<button class="btn btn-info text-white">Mulai Kerja</button></form>
    </div></div>
    @endif

    @if($order->status->value === 'dikerjakan')
    <div class="card border-0 shadow-sm border-start border-4 border-info mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Pekerjaan Selesai?</h6>
        <form action="{{ route('mitra.tenaga.selesai-kerja', $order->id) }}" method="POST">@csrf<button class="btn btn-success">Tandai Selesai</button></form>
    </div></div>
    @endif
</div>
<div class="modal fade" id="tolakModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0">
    <div class="modal-header border-0"><h5 class="fw-bold">Tolak Order</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('mitra.tenaga.tolak', $order->id) }}" method="POST">@csrf
        <div class="modal-body"><textarea name="alasan" class="form-control" rows="3" minlength="10" required></textarea></div>
        <div class="modal-footer border-0"><button class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger">Tolak</button></div>
    </form>
</div></div></div>
@endsection
