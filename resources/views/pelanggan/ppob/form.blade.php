@extends('layouts.pelanggan')
@section('title', 'Beli PPOB')
@section('content')
<div class="container py-4" style="max-width: 540px;">
    <a href="{{ route('pelanggan.ppob.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    <h4 class="fw-bold mb-3">Beli Pulsa / Paket Data / Token</h4>

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('pelanggan.ppob.transaksi') }}" method="POST">@csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Jenis Produk</label>
                    <select name="jenis_produk" class="form-select" required>
                        <option value="pulsa">📱 Pulsa</option>
                        <option value="paket_data">📶 Paket Data</option>
                        <option value="token_listrik">⚡ Token Listrik</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nomor Tujuan / Meter ID</label>
                    <input type="text" name="nomor_tujuan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Kode Produk Digiflazz</label>
                    <input type="text" name="kode_produk" class="form-control" placeholder="Mis: ABCD123" required>
                    <div class="form-text small">Kode SKU dari Digiflazz (admin set di master).</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nama Produk</label>
                    <input type="text" name="nama_produk" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Harga Modal</label>
                        <input type="number" name="harga_modal" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small">Harga Jual</label>
                        <input type="number" name="harga_jual" class="form-control" required>
                    </div>
                </div>
                <button class="btn btn-warning w-100 fw-bold">Beli Sekarang</button>
            </form>
        </div>
    </div>
</div>
@endsection
