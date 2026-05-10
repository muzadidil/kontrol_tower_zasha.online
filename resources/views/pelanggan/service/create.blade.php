@extends('layouts.pelanggan')
@section('title', 'Order Service')
@section('content')
<div class="container py-4" style="max-width: 640px;">
    <a href="{{ url()->previous() }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h5 class="fw-bold">{{ $layanan->masterLayanan->nama_layanan }}</h5>
        <div class="text-muted small">{{ $layanan->mitra->nama_asli ?? '-' }}</div>
        <div class="mt-2 small">Biaya Service Standar: <strong>Rp {{ number_format($layanan->biaya_service_standar, 0, ',', '.') }}</strong></div>
        <div class="alert alert-info small mt-2"><i class="fas fa-info-circle me-1"></i>Harga final ditentukan setelah teknisi melakukan diagnosa di lokasi.</div>
    </div></div>

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <form action="{{ route('pelanggan.service.store') }}" method="POST" class="card border-0 shadow-sm">@csrf
        <input type="hidden" name="mitra_layanan_id" value="{{ $layanan->id }}">
        <div class="card-body p-4">
            <div class="mb-3"><label class="form-label fw-semibold small">Alamat Service</label>
                <input type="text" name="alamat_pelanggan" class="form-control" required></div>
            <div class="row g-2 mb-3">
                <div class="col-6"><input type="number" step="any" name="pelanggan_lat" class="form-control" placeholder="Lat"></div>
                <div class="col-6"><input type="number" step="any" name="pelanggan_lng" class="form-control" placeholder="Lng"></div>
            </div>
            <div class="mb-3"><label class="form-label fw-semibold small">Jarak (km)</label>
                <input type="number" step="0.1" name="jarak_km" class="form-control" value="0"></div>
            <div class="mb-3"><label class="form-label fw-semibold small">Keluhan / Masalah</label>
                <textarea name="keluhan_pelanggan" class="form-control" rows="4" minlength="20" required placeholder="Jelaskan masalah yang Anda alami..."></textarea></div>
            <button type="submit" class="btn btn-warning w-100 fw-bold">Pesan Teknisi</button>
        </div>
    </form>
</div>
@endsection
