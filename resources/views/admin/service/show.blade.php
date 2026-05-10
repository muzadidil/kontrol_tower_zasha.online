@extends('layouts.admin')
@section('title', 'Detail Service ' . $serviceOrder->order_code)
@section('content')
<div class="container-fluid py-4" style="max-width: 860px;">
    <a href="{{ route('admin.service.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    @foreach(['success','error'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
                <h5 class="fw-bold">{{ $serviceOrder->order_code }}</h5>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6"><div class="text-muted small">Pelanggan</div><div>{{ $serviceOrder->pelanggan->nama_pelanggan ?? '-' }}</div></div>
                    <div class="col-md-6"><div class="text-muted small">Mitra</div><div>{{ $serviceOrder->mitra->nama_asli ?? '-' }}</div></div>
                </div>
                <div class="mt-3"><div class="text-muted small">Keluhan</div>
                    <div class="bg-light rounded p-3 small">{{ $serviceOrder->keluhan_pelanggan }}</div></div>
            </div></div>

            @if($serviceOrder->orderItems->count() > 0)
            <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
                <h6 class="fw-bold mb-3">Item Diagnosa</h6>
                @foreach($serviceOrder->orderItems as $item)
                <div class="d-flex justify-content-between mb-1 small">
                    <span><span class="badge bg-{{ $item->tipe === 'jasa' ? 'info' : 'secondary' }}">{{ ucfirst($item->tipe) }}</span> {{ $item->nama_item }}</span>
                    <span class="fw-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div></div>
            @endif

            @if($dispute)
            <div class="card border-0 shadow-sm border-start border-4 border-danger"><div class="card-body p-4">
                <h6 class="fw-bold text-danger">Dispute</h6>
                <div class="bg-light rounded p-3 small mb-3">{{ $dispute->complaint_description }}</div>
                @if($dispute->status === 'open')
                <form action="{{ route('admin.service.resolusi', $serviceOrder->id) }}" method="POST">@csrf
                    <select name="resolusi" class="form-select mb-2" required>
                        <option value="">Pilih</option>
                        <option value="release_to_mitra">Mitra Menang</option>
                        <option value="refund_to_customer">Refund</option>
                    </select>
                    <textarea name="admin_notes" class="form-control mb-2" rows="2" placeholder="Catatan"></textarea>
                    <button class="btn btn-danger">Selesaikan</button>
                </form>
                @endif
            </div></div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body p-4">
                <h6 class="fw-bold mb-3">Rincian</h6>
                <div class="d-flex justify-content-between small mb-1"><span>Jasa</span><span>Rp {{ number_format($serviceOrder->total_jasa, 0, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Sparepart</span><span>Rp {{ number_format($serviceOrder->total_sparepart, 0, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between small mb-1"><span>Bensin</span><span>Rp {{ number_format($serviceOrder->biaya_bensin, 0, ',', '.') }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold"><span>Total</span><span class="text-warning">Rp {{ number_format($serviceOrder->total_biaya, 0, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between small mt-2"><span>Komisi (5% jasa)</span><span class="text-warning">Rp {{ number_format($serviceOrder->komisi_zasha, 0, ',', '.') }}</span></div>
            </div></div>
        </div>
    </div>
</div>
@endsection
