@extends('layouts.pelanggan')
@section('title', 'Order Tenaga')
@section('content')
<div class="container py-4" style="max-width: 640px;">
    <a href="{{ url()->previous() }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h5 class="fw-bold">{{ $layanan->masterLayanan->nama_layanan }}</h5>
            <div class="text-muted small">{{ $layanan->mitra->nama_asli ?? $layanan->mitra->nama_panggilan }}</div>
            <div class="mt-2 small">
                <span class="me-3">Per Jam: <strong>Rp {{ number_format($layanan->tarif_per_jam, 0, ',', '.') }}</strong></span>
                <span>Per Hari: <strong>Rp {{ number_format($layanan->tarif_per_hari, 0, ',', '.') }}</strong></span>
            </div>
        </div>
    </div>

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <form action="{{ route('pelanggan.tenaga.store') }}" method="POST" class="card border-0 shadow-sm">
        @csrf
        <input type="hidden" name="mitra_layanan_id" value="{{ $layanan->id }}">
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Tipe Waktu</label>
                <div class="btn-group w-100" role="group">
                    <input type="radio" class="btn-check" name="tipe_waktu" id="instan" value="instan" checked>
                    <label class="btn btn-outline-warning" for="instan">⚡ Instan</label>
                    <input type="radio" class="btn-check" name="tipe_waktu" id="terjadwal" value="terjadwal">
                    <label class="btn btn-outline-warning" for="terjadwal">📅 Terjadwal</label>
                </div>
            </div>

            <div class="mb-3" id="jadwalInput" style="display:none;">
                <label class="form-label fw-semibold small">Jadwal</label>
                <input type="datetime-local" name="jadwal_at" class="form-control">
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold small">Tipe Durasi</label>
                    <select name="tipe_durasi" class="form-select">
                        <option value="jam">Per Jam</option>
                        <option value="hari">Per Hari</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold small">Durasi</label>
                    <input type="number" name="durasi" class="form-control" step="0.5" min="0.5" value="1" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Metode Pembayaran</label>
                <select name="metode_pembayaran" class="form-select" required>
                    <option value="cod">COD (Bayar di tempat)</option>
                    <option value="saldo">Potong Saldo</option>
                    <option value="transfer">Transfer</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Alamat Pekerjaan</label>
                <input type="text" name="alamat_pelanggan" class="form-control" required>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6"><input type="number" step="any" name="pelanggan_lat" class="form-control" placeholder="Latitude"></div>
                <div class="col-6"><input type="number" step="any" name="pelanggan_lng" class="form-control" placeholder="Longitude"></div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Keterangan</label>
                <textarea name="keterangan_kerja" class="form-control" rows="3" placeholder="Detail pekerjaan..."></textarea>
            </div>

            <button type="submit" class="btn btn-warning w-100 fw-bold">Pesan Sekarang</button>
        </div>
    </form>
</div>
<script>
document.querySelectorAll('input[name="tipe_waktu"]').forEach(el => {
    el.addEventListener('change', e => {
        document.getElementById('jadwalInput').style.display = e.target.value === 'terjadwal' ? 'block' : 'none';
    });
});
</script>
@endsection
