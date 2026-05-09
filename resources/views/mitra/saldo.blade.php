@extends('layouts.mitra')

@section('content')
<div class="container py-3">
    <h5 class="fw-bold mb-4 pt-2">Saldo & Penarikan</h5>

    {{-- Saldo Card --}}
    <div class="card card-custom p-4 mb-4 border-0" style="background:linear-gradient(135deg,#0a5c36,#1a7a4a);">
        <small class="text-white-50 fw-bold d-block mb-1" style="font-size:10px;">SALDO TERSEDIA</small>
        <h3 class="fw-bold text-white mb-0 allow-select">
            Rp {{ number_format($mitra->saldo_mitra ?? $mitra->saldo ?? 0, 0, ',', '.') }}
        </h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success small rounded-3 py-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger small rounded-3 py-2">{{ session('error') }}</div>
    @endif

    {{-- Form Tarik Dana --}}
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold small text-muted text-uppercase mb-3">Tarik Dana</h6>
        <form action="{{ route('mitra.withdrawal.request') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Bank / E-Wallet</label>
                <input type="text" name="bank_name" class="form-control rounded-3" placeholder="BCA, Mandiri, GoPay..." required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nomor Rekening</label>
                <input type="text" name="account_number" class="form-control rounded-3" placeholder="Nomor rekening tujuan" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Pemilik Rekening</label>
                <input type="text" name="account_name" class="form-control rounded-3" placeholder="Sesuai buku tabungan" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Nominal (Min. Rp 50.000)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="nominal" class="form-control rounded-end-3" placeholder="50000" min="50000" required>
                </div>
            </div>
            <button type="submit" class="btn w-100 rounded-pill fw-bold text-white" style="background:#0a5c36;">
                <i class="bi bi-arrow-down-circle me-2"></i>Ajukan Penarikan
            </button>
        </form>
    </div>

    {{-- Riwayat Penarikan --}}
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Riwayat Penarikan</h6>
    @forelse($riwayat as $r)
        <div class="card card-custom p-3 mb-2">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold small">{{ $r->bank_name }} — {{ $r->account_number }}</div>
                    <div class="text-muted" style="font-size:0.7rem;">{{ $r->account_name }}</div>
                    <div class="text-muted" style="font-size:0.65rem;">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold small text-danger">-Rp {{ number_format($r->nominal, 0, ',', '.') }}</div>
                    <span class="badge rounded-pill
                        @if($r->status === 'approved') bg-success
                        @elseif($r->status === 'rejected') bg-danger
                        @else bg-warning text-dark @endif"
                        style="font-size:0.6rem;">
                        {{ ucfirst($r->status) }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-muted small">Belum ada riwayat penarikan.</div>
    @endforelse
</div>
@endsection
