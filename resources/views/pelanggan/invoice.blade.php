@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; --zasha-bg: #f4f7fe; }
    .invoice-box { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: none; }
    .status-waiting { background: #fff9e6; border: 1px dashed #ffcc00; border-radius: 15px; padding: 15px; margin-bottom: 20px; }
    .spinner-custom { width: 1.5rem; height: 1.5rem; border-width: 0.2em; color: #ffcc00; }
    .modal-zasha { border-radius: 30px !important; border: none !important; overflow: hidden; }
    .btn-zasha { background: var(--zasha-blue); color: white; border-radius: 15px; font-weight: 800; padding: 12px 30px; border: none; width: 100%; transition: 0.3s; }
    .icon-cancel { font-size: 4rem; color: #ff3b30; margin-bottom: 20px; display: block; }
</style>

<div class="container py-5 animate-in">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="invoice-box">
                <div class="status-waiting text-center">
                    <div class="spinner-border spinner-custom mb-2" role="status"></div>
                    <p class="small fw-bold mb-0">Menunggu konfirmasi dari {{ $d->nama_mitra }}...</p>
                    <small class="text-muted">Halaman otomatis berpindah</small>
                </div>

                <div class="text-center mb-4">
                    <h5 class="fw-bold mb-0">DETAIL INVOICE</h5>
                    <small class="text-muted">ID Pesanan: #{{ $d->id_pesanan }}</small>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block">Nama Pelanggan:</label>
                    <span class="fw-bold">{{ $d->nama_pelanggan }}</span>
                </div>

                <div class="mb-3">
                    <label class="small text-muted d-block">Alamat Pengantaran:</label>
                    <span class="small d-block">{{ $d->alamat_pelanggan }}</span>
                </div>

                <hr class="my-4 opacity-25">

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Layanan:</span>
                    <span class="fw-bold">{{ $d->kategori_jasa }}</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-muted">Total Bayar:</span>
                    <span class="fw-bold text-primary fs-5">Rp {{ number_format($d->total_pesanan, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('pelanggan.dashboard') }}" class="btn btn-outline-secondary w-100 rounded-pill fw-bold border-0">Batal & Kembali</a>
            </div>
        </div>
    </div>
</div>

{{-- MODAL BATAL --}}
<div class="modal fade" id="modalBatal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered px-4">
        <div class="modal-content modal-zasha shadow-lg">
            <div class="modal-body text-center p-5">
                <i class="bi bi-x-circle-fill icon-cancel"></i>
                <h5 class="fw-bold mb-2">Mohon Maaf...</h5>
                <p class="text-muted small mb-4">Mitra sedang tidak bisa menerima pesanan. Silahkan cari mitra yang lain ya!</p>
                <button type="button" class="btn-zasha shadow-sm" onclick="window.location.href='{{ route('pelanggan.dashboard') }}'">CARI MITRA LAIN</button>
            </div>
        </div>
    </div>
</div>

<script>
    const id_pesanan = "{{ $d->id_pesanan }}";
    const modalBatal = new bootstrap.Modal(document.getElementById('modalBatal'));

    function cekStatusOrder() {
        // Panggil route Laravel API
        fetch(`/invoice/cek-status/${id_pesanan}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'Proses') {
                window.location.href = '/status-pesanan/' + id_pesanan;
            } 
            else if (data.status === 'Batal' || data.status === 'Dibatalkan') {
                clearInterval(intervalCek);
                modalBatal.show();
            }
        })
        .catch(err => console.log("Mengecek status..."));
    }

    const intervalCek = setInterval(cekStatusOrder, 4000);
</script>
@endsection
