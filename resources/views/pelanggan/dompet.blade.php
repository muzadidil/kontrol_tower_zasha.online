@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .header-dompet { background: var(--zasha-blue); color: white; padding: 30px 20px 80px 20px; border-radius: 0 0 30px 30px; text-align: center; }
    .card-saldo { background: linear-gradient(135deg, #002d72, #0047b3); border: none; border-radius: 20px; color: white; padding: 25px; margin-top: -60px; box-shadow: 0 10px 20px rgba(0,45,114,0.2); }
    .btn-topup { background: #ffc107; color: #000; border: none; border-radius: 50px; font-weight: 800; padding: 12px 25px; width: 100%; transition: 0.3s; }
    .form-topup { display: none; background: white; border-radius: 20px; padding: 20px; margin-top: 15px; border: 1px solid #ddd; }
    .riwayat-item { background: white; border-radius: 15px; padding: 15px; margin-bottom: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .instruksi-bayar { background: #fff; border: 2px solid var(--zasha-blue); border-radius: 15px; padding: 20px; margin-top: 15px; }
    .warning-text { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; padding: 15px; border-radius: 10px; font-size: 0.75rem; line-height: 1.5; text-align: left; }
</style>

<div class="header-dompet">
    <h5 class="fw-bold m-0">Dompet ZASHA</h5>
    <p class="small opacity-75">Saldo belanja jastip & jastip jember</p>
</div>

<div class="container text-center mb-4">
    <div class="card card-saldo mb-4 animate-in">
        <span class="small opacity-75 fw-bold text-uppercase">Saldo Saya</span>
        <h2 class="fw-bold mt-1 mb-4">Rp {{ number_format($pelanggan->saldo ?? 0, 0, ',', '.') }}</h2>
        <button onclick="toggleForm()" class="btn btn-topup shadow-sm">
            <i class="bi bi-plus-circle-fill me-2"></i> ISI SALDO (TOP UP)
        </button>
    </div>

    @if(session('notif_topup') == 'sukses')
        <div class="instruksi-bayar shadow-sm mb-4 animate-in">
            <small class="text-muted d-block fw-bold">TRANSFER TEPAT SEJUMLAH:</small>
            <h1 class="fw-bold text-primary mt-1 mb-3">Rp {{ number_format(session('data_transfer'), 0, ',', '.') }}</h1>
            <div class="p-3 bg-light rounded-3 mb-3">
                <small class="text-muted d-block text-uppercase fw-bold">Ke Rekening ({{ session('data_bank') }}):</small>
                @if(session('data_bank') == 'BCA')
                    <span class="fw-bold fs-5">1470807381</span><br>
                    <span class="small fw-bold">a/n Muzadidil Fuad</span>
                @else
                    <span class="fw-bold fs-5">082232458226</span><br>
                    <span class="small fw-bold">A/n Muzadidil Fuad</span>
                @endif
            </div>
            <div class="warning-text mb-3">
                <strong><i class="bi bi-exclamation-triangle-fill"></i> PENTING:</strong><br>
                Mohon transfer sesuai nominal hingga 3 digit terakhir agar otomatis terdeteksi.
            </div>
        </div>
    @endif

    <div id="areaForm" class="form-topup shadow-sm text-start">
        <form action="{{ route('pelanggan.dompet.topup') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="small fw-bold">Pilih Media Transfer</label>
                <select name="bank_tujuan" class="form-select" required>
                    <option value="DANA">DANA (Muzadidil Fuad)</option>
                    <option value="BCA">BCA (Muzadidil Fuad)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="small fw-bold">Nominal Isi Saldo</label>
                <input type="number" name="nominal" class="form-control" placeholder="Contoh: 50000" required>
            </div>
            <div class="mb-3">
                <label class="small fw-bold">Nama Pengirim (Sesuai Rekening)</label>
                <input type="text" name="nomor_rekening" class="form-control" placeholder="Contoh: Siti Aminah" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">KONFIRMASI SEKARANG</button>
        </form>
    </div>

    <h6 class="fw-bold mt-4 mb-3 small text-muted text-uppercase text-start">Riwayat Isi Saldo</h6>
    @forelse($riwayat as $r)
        <div class="riwayat-item border p-3 text-start">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-bold">Rp {{ number_format($r->total_transfer, 0, ',', '.') }}</span>
                @php 
                    $badge = "bg-warning text-dark"; 
                    if($r->status == 'sukses') $badge = "bg-success text-white";
                    if($r->status == 'batal') $badge = "bg-danger text-white";
                @endphp
                <span class="badge {{ $badge }} rounded-pill" style="font-size: 0.65rem;">{{ ucfirst($r->status) }}</span>
            </div>
            <div class="d-flex justify-content-between small text-muted">
                <span>Via {{ $r->bank_tujuan }}</span>
                <span>{{ date('d/m, H:i', strtotime($r->waktu_request)) }}</span>
            </div>
        </div>
    @empty
        <p class="text-muted small">Belum ada riwayat transaksi.</p>
    @endforelse
</div>

<script>
    function toggleForm() {
        var x = document.getElementById("areaForm");
        x.style.display = (x.style.display === "none" || x.style.display === "") ? "block" : "none";
        if(x.style.display === "block") x.scrollIntoView({behavior: "smooth"});
    }
</script>
@endsection
