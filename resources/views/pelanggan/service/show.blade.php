@extends('layouts.pelanggan')
@section('title', 'Detail ' . $order->order_code)
@section('content')
<div class="container py-4" style="max-width: 640px;">
    <a href="{{ route('pelanggan.service.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Daftar Service</a>
    @foreach(['success','error','warning'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','diagnosa'=>'info','menunggu_konfirmasi_harga'=>'warning','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <div class="d-flex justify-content-between"><div><div class="fw-bold fs-5">{{ $order->order_code }}</div></div>
            <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span></div>
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Mitra</h6>
        <div class="fw-semibold">{{ $order->mitra->nama_asli ?? '-' }}</div>
        @if($order->mitra->no_wa) <a href="https://wa.me/{{ $order->mitra->no_wa }}" target="_blank" class="text-success small"><i class="fab fa-whatsapp"></i> {{ $order->mitra->no_wa }}</a> @endif
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Keluhan</h6>
        <div class="bg-light rounded p-3 small">{{ $order->keluhan_pelanggan }}</div>
    </div></div>

    {{-- Item Diagnosa --}}
    @if($order->orderItems->count() > 0)
    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Hasil Diagnosa</h6>
        @foreach($order->orderItems as $item)
        <div class="d-flex justify-content-between mb-2 small">
            <div>
                <span class="badge bg-{{ $item->tipe === 'jasa' ? 'info' : 'secondary' }}">{{ ucfirst($item->tipe) }}</span>
                {{ $item->nama_item }}
                @if($item->catatan) <div class="text-muted">{{ $item->catatan }}</div> @endif
            </div>
            <div class="fw-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</div>
        </div>
        @endforeach
        <hr>
        <div class="d-flex justify-content-between small"><span>Total Jasa</span><span>Rp {{ number_format($order->total_jasa, 0, ',', '.') }}</span></div>
        <div class="d-flex justify-content-between small"><span>Total Sparepart</span><span>Rp {{ number_format($order->total_sparepart, 0, ',', '.') }}</span></div>
        <div class="d-flex justify-content-between small"><span>Bensin</span><span>Rp {{ number_format($order->biaya_bensin, 0, ',', '.') }}</span></div>
        <hr>
        <div class="d-flex justify-content-between fw-bold"><span>Total Biaya</span><span class="text-warning">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</span></div>
    </div></div>
    @endif

    @if($order->status->value === 'menunggu_konfirmasi_harga')
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Setujui Harga & Pilih Pembayaran</h6>
        <form action="{{ route('pelanggan.service.approve-harga', $order->id) }}" method="POST">@csrf
            <select name="metode_pembayaran" class="form-select mb-2" required>
                <option value="">Pilih Metode</option>
                <option value="cod">COD (Bayar di tempat)</option>
                <option value="saldo">Potong Saldo</option>
                <option value="transfer">Transfer</option>
            </select>
            <button class="btn btn-success w-100">Setuju, Mulai Kerja</button>
        </form>
    </div></div>
    @endif

    @if($order->status->value === 'menunggu_konfirmasi')
    <div class="card border-0 shadow-sm border-start border-4 border-warning"><div class="card-body p-4">
        <h6 class="fw-bold">Pekerjaan Selesai?</h6>
        <div class="d-flex gap-2">
            <form action="{{ route('pelanggan.service.konfirmasi', $order->id) }}" method="POST">@csrf<button class="btn btn-success">Konfirmasi</button></form>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal">Ada Masalah</button>
        </div>
    </div></div>
    @endif
</div>
<div class="modal fade" id="disputeModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0">
    <div class="modal-header border-0"><h5 class="fw-bold">Buka Dispute</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('pelanggan.service.dispute', $order->id) }}" method="POST">@csrf
        <div class="modal-body"><textarea name="deskripsi" class="form-control" rows="4" minlength="20" required></textarea></div>
        <div class="modal-footer border-0"><button class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger">Kirim</button></div>
    </form>
</div></div></div>
@endsection
