@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .header-detail { background: white; position: sticky; top: 0; z-index: 1020; padding: 15px; border-bottom: 1px solid #eee; }
    .foto-hero { width: 100%; height: 250px; object-fit: cover; background: #e2e8f0; }
    .card-driver-info { border: none; border-radius: 0 0 25px 25px; background: white; padding: 20px; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    .section-card { background: white; border-radius: 20px; padding: 20px; margin: 0 15px 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
    .info-row:last-child { border-bottom: none; }
    .sticky-order { position: fixed; bottom: 70px; left: 0; right: 0; background: white; padding: 12px 20px; border-top: 1px solid #eee; z-index: 999; max-width: 480px; margin: 0 auto; }
</style>

<div class="header-detail shadow-sm">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <h5 class="fw-bold m-0" style="font-size: 1rem;">Detail Driver Jastip</h5>
    </div>
</div>

{{-- Foto Driver --}}
@php
    $foto_url = !empty($driver->foto_driver)
        ? asset('img/' . $driver->foto_driver)
        : 'https://ui-avatars.com/api/?name=' . urlencode($driver->nama_driver ?? 'D') . '&background=002d72&color=fff&size=300';
@endphp
<img src="{{ $foto_url }}" class="foto-hero" alt="{{ $driver->nama_driver }}">

{{-- Info Driver --}}
<div class="card-driver-info">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h4 class="fw-bold mb-1">{{ $driver->nama_driver }}</h4>
            <span class="badge bg-info bg-opacity-10 text-info fw-bold" style="font-size:0.7rem;">
                <i class="bi bi-rocket-takeoff me-1"></i>JASTIP HUNTER
            </span>
        </div>
        <div>
            @if(strtolower($driver->status_kerja ?? '') == 'aktif')
                <span class="badge bg-success fs-7">Online</span>
            @else
                <span class="badge bg-secondary fs-7">Offline</span>
            @endif
        </div>
    </div>
</div>

{{-- Info Detail --}}
<div class="section-card">
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Informasi Driver</h6>

    @if(!empty($driver->jenis_kendaraan))
    <div class="info-row">
        <span class="text-muted"><i class="bi bi-bicycle me-2"></i>Kendaraan</span>
        <span class="fw-bold">{{ $driver->jenis_kendaraan }}</span>
    </div>
    @endif

    @if(!empty($driver->plat_nomor))
    <div class="info-row">
        <span class="text-muted"><i class="bi bi-card-text me-2"></i>Plat Nomor</span>
        <span class="fw-bold">{{ $driver->plat_nomor }}</span>
    </div>
    @endif

    @if(!empty($driver->warna_kendaraan))
    <div class="info-row">
        <span class="text-muted"><i class="bi bi-palette me-2"></i>Warna</span>
        <span class="fw-bold">{{ $driver->warna_kendaraan }}</span>
    </div>
    @endif

    <div class="info-row">
        <span class="text-muted"><i class="bi bi-shield-check me-2"></i>Status Verifikasi</span>
        @if(($driver->status_verifikasi ?? '') == 'Verified')
            <span class="badge bg-success">Terverifikasi</span>
        @else
            <span class="badge bg-warning text-dark">Menunggu</span>
        @endif
    </div>
</div>

{{-- Cara Pesan --}}
<div class="section-card">
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Cara Pesan Jastip</h6>
    <div class="d-flex align-items-start mb-3">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:30px; height:30px; font-size:0.8rem; font-weight:800;">1</div>
        <div>
            <p class="fw-bold small mb-0">Pilih Driver</p>
            <p class="text-muted" style="font-size:0.75rem;">Pilih driver yang sedang online dan terdekat dari lokasimu</p>
        </div>
    </div>
    <div class="d-flex align-items-start mb-3">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:30px; height:30px; font-size:0.8rem; font-weight:800;">2</div>
        <div>
            <p class="fw-bold small mb-0">Isi Detail Pesanan</p>
            <p class="text-muted" style="font-size:0.75rem;">Masukkan lokasi toko, nama barang, dan anggaran belanja</p>
        </div>
    </div>
    <div class="d-flex align-items-start">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width:30px; height:30px; font-size:0.8rem; font-weight:800;">3</div>
        <div>
            <p class="fw-bold small mb-0">Tunggu & Terima</p>
            <p class="text-muted" style="font-size:0.75rem;">Driver akan langsung belanja dan mengantarkan ke lokasimu</p>
        </div>
    </div>
</div>

{{-- Spacer for sticky button + bottom nav --}}
<div style="height: 160px;"></div>

{{-- Sticky Order Button --}}
<div class="sticky-order shadow-lg">
    @if(strtolower($driver->status_kerja ?? '') == 'aktif')
        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow"
                data-bs-toggle="modal" data-bs-target="#modalPesanJastip">
            <i class="bi bi-cart-plus-fill me-2"></i>PESAN JASTIP SEKARANG
        </button>
    @else
        <button class="btn btn-secondary w-100 rounded-pill fw-bold py-3" disabled>
            Driver Sedang Offline
        </button>
    @endif
</div>

{{-- Modal Pesan Jastip --}}
<div class="modal fade" id="modalPesanJastip" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content" style="border-radius: 25px; border: none;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-4 text-center">Form Pesan Jastip</h5>
                <form action="{{ route('pelanggan.pesan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_mitra" value="{{ $driver->id_driver }}">
                    <input type="hidden" name="tipe" value="jastip">

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Lokasi Toko / Pasar</label>
                        <input type="text" name="lokasi_asal" class="form-control rounded-3"
                               placeholder="Contoh: Pasar Tanjung, Jember" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Daftar Belanjaan</label>
                        <textarea name="daftar_belanja" class="form-control rounded-3" rows="3"
                                  placeholder="Tulis nama barang, ukuran, jumlah..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Estimasi Budget Belanja</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" name="total_harga_barang" class="form-control rounded-end-3"
                                   placeholder="0" min="0">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Metode Pembayaran</label>
                        <select name="metode_pembayaran" class="form-select rounded-3" required>
                            <option value="COD">COD (Bayar Saat Diterima)</option>
                            <option value="Transfer">Transfer Bank</option>
                            <option value="Saldo">Saldo ZASHA</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold py-3"
                                data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">
                            KONFIRMASI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
