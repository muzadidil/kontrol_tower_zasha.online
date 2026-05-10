@extends('layouts.pelanggan')
@section('title', 'Buat Order Jastip')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <a href="{{ url()->previous() }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>

    <h4 class="fw-bold mb-3">Buat Order Jastip</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('pelanggan.jastip.store') }}" method="POST" id="jastipForm">
        @csrf

        {{-- Pilih Mitra (Opsional) --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3">Pilih Mitra Jastip <span class="text-muted small">(opsional)</span></h6>
                <select name="mitra_id" class="form-select">
                    <option value="">-- Auto-assign mitra terdekat --</option>
                    @foreach($mitraJastip as $m)
                        <option value="{{ $m->id_mitra }}">{{ $m->nama_asli ?? $m->nama_panggilan }} (Saldo: Rp {{ number_format($m->saldo, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Alamat Pengiriman --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="fas fa-home text-warning me-2"></i>Alamat Pengantaran</h6>
                <input type="text" name="delivery_address" class="form-control mb-2" placeholder="Alamat lengkap pengantaran" required>
                <div class="row g-2">
                    <div class="col-6"><input type="number" step="any" name="delivery_lat" class="form-control" placeholder="Latitude" required></div>
                    <div class="col-6"><input type="number" step="any" name="delivery_lng" class="form-control" placeholder="Longitude" required></div>
                </div>
                <div class="form-text">Tip: gunakan halaman alamat untuk pilih lokasi via maps.</div>
            </div>
        </div>

        {{-- Stops Container --}}
        <div id="stopsContainer">
            {{-- Stops akan di-append di sini --}}
        </div>

        <button type="button" class="btn btn-outline-warning w-100 mb-3 fw-semibold" onclick="tambahStop()">
            <i class="fas fa-plus me-1"></i> Tambah Lokasi Belanja
        </button>

        <div class="alert alert-warning d-flex align-items-start gap-2 mb-3">
            <i class="fas fa-info-circle mt-1"></i>
            <div class="small">
                <strong>Mitra nalangi dulu</strong>, Anda bayar saat barang diantar (COD).<br>
                Total bayar = harga asli barang + ongkos jasa.<br>
                Komisi Zasha 5% diambil dari saldo mitra (bukan dari Anda).
            </div>
        </div>

        <button type="submit" class="btn btn-warning w-100 fw-bold py-2">
            <i class="fas fa-paper-plane me-1"></i> Kirim Order ke Mitra
        </button>
    </form>
</div>

<template id="stopTemplate">
    <div class="card border-0 shadow-sm mb-3 stop-card">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fas fa-map-marker-alt text-danger me-2"></i>Lokasi <span class="stop-num">1</span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusStop(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <input type="text" name="stops[INDEX][nama_lokasi]" class="form-control form-control-sm mb-2" placeholder="Nama toko / lokasi" required>
            <input type="text" name="stops[INDEX][alamat_lokasi]" class="form-control form-control-sm mb-2" placeholder="Alamat lengkap" required>
            <div class="row g-2 mb-3">
                <div class="col-6"><input type="number" step="any" name="stops[INDEX][lat]" class="form-control form-control-sm" placeholder="Lat" required></div>
                <div class="col-6"><input type="number" step="any" name="stops[INDEX][lng]" class="form-control form-control-sm" placeholder="Lng" required></div>
            </div>

            <h6 class="fw-bold mt-3 mb-2 small">Daftar Belanja</h6>
            <div class="items-container"></div>
            <button type="button" class="btn btn-link btn-sm text-warning ps-0 add-item">
                <i class="fas fa-plus me-1"></i> Tambah Item
            </button>
        </div>
    </div>
</template>

<template id="itemTemplate">
    <div class="row g-2 mb-2 item-row">
        <div class="col-5"><input type="text" name="ITEM_NAME[nama_barang]" class="form-control form-control-sm" placeholder="Nama barang" required></div>
        <div class="col-4"><input type="number" name="ITEM_NAME[harga_perkiraan]" class="form-control form-control-sm" placeholder="Estimasi harga" required min="0"></div>
        <div class="col-2"><input type="text" name="ITEM_NAME[catatan]" class="form-control form-control-sm" placeholder="Catatan"></div>
        <div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.item-row').remove()"><i class="fas fa-times"></i></button></div>
    </div>
</template>

<script>
let stopIdx = 0;

function tambahStop() {
    const tpl = document.getElementById('stopTemplate').content.cloneNode(true);
    const card = tpl.querySelector('.stop-card');
    card.dataset.idx = stopIdx;
    card.querySelector('.stop-num').textContent = stopIdx + 1;
    card.querySelectorAll('input').forEach(input => {
        input.name = input.name.replace('INDEX', stopIdx);
    });

    card.querySelector('.add-item').addEventListener('click', () => tambahItem(card));
    document.getElementById('stopsContainer').appendChild(card);
    tambahItem(card);
    stopIdx++;
    renumberStops();
}

function tambahItem(stopCard) {
    const idx = stopCard.dataset.idx;
    const itemsContainer = stopCard.querySelector('.items-container');
    const itemIdx = itemsContainer.children.length;
    const tpl = document.getElementById('itemTemplate').content.cloneNode(true);
    tpl.querySelectorAll('input').forEach(input => {
        input.name = input.name.replace('ITEM_NAME', `stops[${idx}][items][${itemIdx}]`);
    });
    itemsContainer.appendChild(tpl);
}

function hapusStop(btn) {
    btn.closest('.stop-card').remove();
    renumberStops();
}

function renumberStops() {
    document.querySelectorAll('.stop-card').forEach((card, i) => {
        card.querySelector('.stop-num').textContent = i + 1;
    });
}

document.addEventListener('DOMContentLoaded', () => tambahStop());
</script>
@endsection
