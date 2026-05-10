@extends('layouts.pelanggan')
@section('title', 'Topup Saldo')
@section('content')
<div class="container py-4" style="max-width: 480px;">
    <a href="{{ route('pelanggan.dompet') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Dompet</a>
    <h4 class="fw-bold mb-3">Topup Saldo</h4>

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('pelanggan.topup.store') }}" method="POST">@csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Jumlah Topup</label>
                    <input type="number" name="jumlah" class="form-control form-control-lg" min="10000" step="1000" placeholder="Min: 10,000" required>
                </div>
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    @foreach([20000, 50000, 100000, 200000, 500000] as $nom)
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="document.querySelector('[name=jumlah]').value={{ $nom }}">Rp {{ number_format($nom, 0, ',', '.') }}</button>
                    @endforeach
                </div>
                <div class="alert alert-info small d-flex align-items-start gap-2">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div>Pembayaran via Tokopay (QRIS, Bank Transfer, e-Wallet). Setelah klik tombol di bawah, Anda akan diarahkan ke halaman pembayaran.</div>
                </div>
                <button class="btn btn-warning w-100 fw-bold">Lanjut Bayar</button>
            </form>
        </div>
    </div>

    <h6 class="mt-4 mb-2 text-muted small">Riwayat Topup</h6>
    @php $topups = \App\Models\TopupRequest::where('user_type', 'pelanggan')->where('user_id', auth('pelanggan')->id())->latest()->limit(5)->get(); @endphp
    @foreach($topups as $tp)
    <div class="card border-0 shadow-sm mb-2"><div class="card-body p-2 d-flex justify-content-between small">
        <div>{{ $tp->created_at->format('d M H:i') }}</div>
        <div>Rp {{ number_format($tp->jumlah, 0, ',', '.') }} ·
            @php $bm = ['pending'=>'warning','success'=>'success','failed'=>'danger']; @endphp
            <span class="badge bg-{{ $bm[$tp->status] ?? 'secondary' }}">{{ ucfirst($tp->status) }}</span>
        </div>
    </div></div>
    @endforeach
</div>
@endsection
