@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .header-detail { background: white; position: sticky; top: 0; z-index: 1020; padding: 15px; border-bottom: 1px solid #eee; }
    .foto-hero { width: 100%; height: 250px; object-fit: cover; background: #e2e8f0; }
    .card-mitra-info { border: none; border-radius: 0 0 25px 25px; background: white; padding: 20px; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
    .rating-stars { color: #ffc107; }
    .price-tag { color: var(--zasha-blue); font-weight: 800; font-size: 1.3rem; }
    .section-card { background: white; border-radius: 20px; padding: 20px; margin: 0 15px 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .ulasan-item { border-bottom: 1px solid #f1f5f9; padding: 12px 0; }
    .ulasan-item:last-child { border-bottom: none; }
    .sticky-order { position: fixed; bottom: 70px; left: 0; right: 0; background: white; padding: 12px 20px; border-top: 1px solid #eee; z-index: 999; max-width: 480px; margin: 0 auto; }
</style>

<div class="header-detail shadow-sm">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <h5 class="fw-bold m-0" style="font-size: 1rem;">Detail Mitra</h5>
    </div>
</div>

{{-- Foto Mitra --}}
@php
    $foto_url = !empty($mitra->foto_mitra)
        ? asset('img/' . $mitra->foto_mitra)
        : 'https://ui-avatars.com/api/?name=' . urlencode($mitra->nama_panggilan ?? 'M') . '&background=002d72&color=fff&size=300';
@endphp
<img src="{{ $foto_url }}" class="foto-hero" alt="{{ $mitra->nama_panggilan }}">

{{-- Info Mitra --}}
<div class="card-mitra-info">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h4 class="fw-bold mb-1">{{ $mitra->nama_panggilan }}</h4>
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold" style="font-size:0.7rem;">
                <i class="bi bi-tools me-1"></i>{{ $mitra->nama_kategori ?? 'Jasa' }}
            </span>
        </div>
        <div class="text-end">
            <div class="price-tag">Rp {{ number_format($mitra->tarif_per_jam ?? 0, 0, ',', '.') }}</div>
            <small class="text-muted">/ {{ $mitra->satuan_tarif ?? 'Jam' }}</small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3 mt-3">
        <div class="d-flex align-items-center">
            <span class="rating-stars me-1"><i class="bi bi-star-fill"></i></span>
            <span class="fw-bold small">{{ number_format($rating_rata, 1) }}</span>
            <span class="text-muted small ms-1">({{ $total_order }} order)</span>
        </div>
        <div>
            @if($mitra->status_online === 'online')
                <span class="badge bg-success">Online</span>
            @elseif($mitra->status_online === 'sibuk')
                <span class="badge" style="background: #ede9fe; color: #7c3aed;">Sedang Bekerja</span>
            @else
                <span class="badge bg-secondary">Offline</span>
            @endif
        </div>
    </div>
</div>

{{-- Deskripsi --}}
@if(!empty($mitra->deskripsi_singkat))
<div class="section-card">
    <h6 class="fw-bold small text-muted text-uppercase mb-2">Tentang</h6>
    <p class="small mb-0 text-muted">{{ $mitra->deskripsi_singkat }}</p>
</div>
@endif

{{-- Tarif --}}
<div class="section-card">
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Rincian Tarif</h6>
    <div class="d-flex justify-content-between small mb-2">
        <span class="text-muted">Tarif per {{ $mitra->satuan_tarif ?? 'Jam' }}</span>
        <span class="fw-bold">Rp {{ number_format($mitra->tarif_per_jam ?? 0, 0, ',', '.') }}</span>
    </div>
    @if(!empty($mitra->tarif_per_hari))
    <div class="d-flex justify-content-between small mb-2">
        <span class="text-muted">Tarif per Hari</span>
        <span class="fw-bold">Rp {{ number_format($mitra->tarif_per_hari, 0, ',', '.') }}</span>
    </div>
    @endif
    @if(!empty($mitra->biaya_service_standar) && $mitra->biaya_service_standar > 0)
    <div class="d-flex justify-content-between small">
        <span class="text-muted">Biaya Service Standar</span>
        <span class="fw-bold">Rp {{ number_format($mitra->biaya_service_standar, 0, ',', '.') }}</span>
    </div>
    @endif
</div>

{{-- Ulasan --}}
@if($ulasans->isNotEmpty())
<div class="section-card">
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Ulasan Pelanggan</h6>
    @foreach($ulasans as $u)
    <div class="ulasan-item">
        <div class="d-flex align-items-center mb-1">
            <span class="fw-bold small me-2">{{ $u->nama_pelanggan }}</span>
            <span class="rating-stars" style="font-size:0.75rem;">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= $u->rating ? '-fill' : '' }}"></i>
                @endfor
            </span>
        </div>
        @if(!empty($u->ulasan))
            <p class="small text-muted mb-0">{{ $u->ulasan }}</p>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- Spacer for sticky button + bottom nav --}}
<div style="height: 160px;"></div>

{{-- Sticky Order Button --}}
<div class="sticky-order shadow-lg">
    @if($mitra->status_online === 'online')
        <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-3 shadow"
                data-bs-toggle="modal" data-bs-target="#modalPesan">
            <i class="bi bi-bag-plus-fill me-2"></i>PESAN SEKARANG
        </button>
    @elseif($mitra->status_online === 'sibuk')
        <button class="btn btn-secondary w-100 rounded-pill fw-bold py-3" disabled>
            Mitra Sedang Bekerja
        </button>
    @else
        <button class="btn btn-secondary w-100 rounded-pill fw-bold py-3" disabled>
            Mitra Sedang Offline
        </button>
    @endif
</div>

{{-- Modal Pesan --}}
<div class="modal fade" id="modalPesan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content" style="border-radius: 25px; border: none;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-4 text-center">Tipe Pemesanan</h5>

                {{-- Toggle Tipe Order --}}
                <div class="mb-4">
                    <div class="btn-group w-100 d-flex" role="group">
                        <input type="radio" class="btn-check" name="tipe_order" id="orderLangsung" value="langsung" checked>
                        <label class="btn btn-outline-primary flex-grow-1 rounded-start-3 fw-bold" for="orderLangsung">
                            <i class="bi bi-lightning-fill me-1"></i>Langsung
                        </label>
                        <input type="radio" class="btn-check" name="tipe_order" id="orderInden" value="inden">
                        <label class="btn btn-outline-primary flex-grow-1 rounded-end-3 fw-bold" for="orderInden">
                            <i class="bi bi-calendar-event me-1"></i>Inden
                        </label>
                    </div>
                    <small class="text-muted d-block mt-2" id="tipeDesc">Pesan sekarang, kerjakan hari ini</small>
                </div>

                <form id="formOrder" action="{{ route('pelanggan.pesan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_mitra" value="{{ $mitra->id_mitra }}">

                    {{-- Durasi --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Durasi Kerja</label>
                        <div class="input-group">
                            <input type="number" name="durasi" id="inputDurasi" class="form-control rounded-start-3"
                                   min="1" max="24" value="1" required>
                            <span class="input-group-text rounded-end-3" id="satuanInput">{{ $mitra->satuan_tarif ?? 'Jam' }}</span>
                        </div>
                    </div>

                    {{-- Tanggal Pelaksanaan (Hidden, Muncul jika Inden) --}}
                    <div class="mb-3" id="sectionTanggal" style="display: none;">
                        <label class="form-label fw-bold small">Tanggal Pelaksanaan</label>
                        <input type="date" name="tanggal_pelaksanaan" id="inputTanggal" class="form-control rounded-3">
                        <small class="text-muted d-block mt-1">Minimal besok</small>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Metode Pembayaran</label>
                        <select name="metode_pembayaran" id="metodeSelect" class="form-select rounded-3" required>
                            <option value="saldo">Saldo ZASHA</option>
                            <option value="transfer">Transfer Bank</option>
                            <option value="cod">COD (Bayar di Tempat)</option>
                        </select>
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Keterangan (Opsional)</label>
                        <textarea name="keterangan_kerja" class="form-control rounded-3" rows="3"
                                  placeholder="Jelaskan pekerjaan yang dibutuhkan..."></textarea>
                    </div>

                    {{-- Rincian Harga --}}
                    <div style="background: #f8fafc; border-radius: 15px; padding: 15px; margin-bottom: 20px; font-size: 13px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tarif <span id="labelTarif">/ Jam</span></span>
                            <span class="fw-bold" id="displayTarif">Rp {{ number_format($mitra->tarif_per_jam ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Durasi</span>
                            <span class="fw-bold"><span id="displayDurasi">1</span> <span id="satuanDisplay">Jam</span></span>
                        </div>
                        <hr style="margin: 10px 0; border-color: #ddd;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total Biaya</span>
                            <span class="fw-bold text-primary" id="displayTotal" style="font-size: 15px;">
                                Rp {{ number_format($mitra->tarif_per_jam ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        {{-- DP Info (Hidden, Muncul jika Inden) --}}
                        <div id="dpInfo" style="display: none; background: white; padding: 10px; border-radius: 10px; border-left: 3px solid #f0a500;">
                            <small class="d-block text-muted mb-1">DP 50% yang harus dibayar</small>
                            <strong style="color: #f0a500; font-size: 14px;" id="displayDp">
                                Rp 0
                            </strong>
                        </div>
                    </div>

                    {{-- Form Fields Tambahan untuk Inden --}}
                    <input type="hidden" name="tarif_per_jam" id="hargaJam" value="{{ $mitra->tarif_per_jam ?? 0 }}">
                    <input type="hidden" name="tarif_per_hari" id="hargaHari" value="{{ $mitra->tarif_per_hari ?? 0 }}">
                    <input type="hidden" id="alamatPelanggan" name="alamat_pelanggan" value="">
                    <input type="hidden" id="pelangganLat" name="pelanggan_lat" value="">
                    <input type="hidden" id="pelangganLng" name="pelanggan_lng" value="">

                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold py-3"
                                data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow" id="btnSubmit">
                            KONFIRMASI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const radioLangsung = document.getElementById('orderLangsung');
    const radioInden = document.getElementById('orderInden');
    const formOrder = document.getElementById('formOrder');
    const inputDurasi = document.getElementById('inputDurasi');
    const inputTanggal = document.getElementById('inputTanggal');
    const sectionTanggal = document.getElementById('sectionTanggal');
    const dpInfo = document.getElementById('dpInfo');
    const tipeDesc = document.getElementById('tipeDesc');
    const btnSubmit = document.getElementById('btnSubmit');
    const metodeSelect = document.getElementById('metodeSelect');

    const tariffJam = {{ $mitra->tarif_per_jam ?? 0 }};
    const tariffHari = {{ $mitra->tarif_per_hari ?? 0 }};
    const mitraId = '{{ $mitra->id_mitra }}';
    const alamatUtama = '{{ $alamats->firstWhere("is_utama", true)?->alamat ?? "Alamat belum diisi" }}';

    function updatePricing() {
        const durasi = parseInt(inputDurasi.value) || 1;
        const tipeOrder = radioInden.checked ? 'inden' : 'langsung';

        let satuan, tarif;
        if (tipeOrder === 'langsung') {
            satuan = '{{ $mitra->satuan_tarif ?? "Jam" }}';
            tarif = tariffJam;
        } else {
            satuan = 'Jam'; // Untuk sekarang assume indent selalu per Jam
            tarif = tariffJam;
        }

        const total = Math.round(tarif * durasi);
        const dp = Math.round(total * 0.5);

        document.getElementById('displayTarif').textContent = 'Rp ' + tarif.toLocaleString('id-ID');
        document.getElementById('displayDurasi').textContent = durasi;
        document.getElementById('satuanInput').textContent = satuan;
        document.getElementById('satuanDisplay').textContent = satuan;
        document.getElementById('labelTarif').textContent = '/ ' + satuan;
        document.getElementById('displayTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('displayDp').textContent = 'Rp ' + dp.toLocaleString('id-ID');
        document.getElementById('hargaJam').value = tariffJam;
        document.getElementById('hargaHari').value = tariffHari;
    }

    function updateTipeOrder() {
        const isInden = radioInden.checked;

        if (isInden) {
            sectionTanggal.style.display = 'block';
            dpInfo.style.display = 'block';
            tipeDesc.textContent = 'Pesan untuk tanggal tertentu di masa depan';
            btnSubmit.textContent = 'BOOKING & BAYAR DP';
            formOrder.action = '{{ route("pelanggan.inden.store") }}';
            metodeSelect.value = 'saldo';
        } else {
            sectionTanggal.style.display = 'none';
            dpInfo.style.display = 'none';
            tipeDesc.textContent = 'Pesan sekarang, kerjakan hari ini';
            btnSubmit.textContent = 'KONFIRMASI';
            formOrder.action = '{{ route("pelanggan.pesan") }}';
        }

        updatePricing();
    }

    function setMinDate() {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        inputTanggal.min = tomorrow.toISOString().split('T')[0];
        // Set nilai default ke besok
        inputTanggal.value = tomorrow.toISOString().split('T')[0];
    }

    // Set alamat pelanggan
    document.getElementById('alamatPelanggan').value = alamatUtama;

    // Event listeners
    radioLangsung.addEventListener('change', updateTipeOrder);
    radioInden.addEventListener('change', updateTipeOrder);
    inputDurasi.addEventListener('input', updatePricing);

    // Initialize
    setMinDate();
    updateTipeOrder();
})();
</script>
@endpush
@endsection
