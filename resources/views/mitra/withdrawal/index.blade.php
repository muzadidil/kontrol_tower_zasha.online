@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

@php
    $mitra = Auth::guard('mitra')->user();
    $totalPending = $withdrawals->where('status', 'pending')->sum('nominal');
@endphp

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Keuangan</div>
            <h1 class="m-hero-title">Tarik Saldo</h1>
        </div>
    </div>

    <div style="text-align:center;">
        <div class="m-hero-stat-label">SALDO TERSEDIA</div>
        <div class="m-hero-stat-value">Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}</div>
        @if($totalPending > 0)
            <div class="m-hero-stat-sub">Rp {{ number_format($totalPending, 0, ',', '.') }} sedang diproses</div>
        @endif
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="m-alert m-alert-error" style="flex-direction:column; align-items:flex-start;">
            @foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill"></i>{{ $err }}</div>@endforeach
        </div>
    @endif

    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">
                <i class="bi bi-bank m-section-title-icon"></i> Permintaan Penarikan Baru
            </h2>

            <form action="{{ route('mitra.withdrawal.request') }}" method="POST">
                @csrf
                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">Nominal</label>
                    <div style="display:flex; align-items:stretch; border:1px solid var(--line); border-radius:var(--r-md); overflow:hidden;">
                        <span style="padding:var(--fib-2) var(--fib-3); background:var(--mitra-blue-tint); color:var(--ink-soft); font-size:var(--t-sm);">Rp</span>
                        <input type="number" name="nominal" class="m-form-input" style="border:none; border-radius:0;"
                               min="50000" max="10000000" step="1000" placeholder="100000"
                               value="{{ old('nominal') }}" required>
                    </div>
                    <div class="m-form-help">Min Rp 50.000 · Max Rp 10.000.000</div>
                </div>

                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">Bank</label>
                    <input type="text" name="bank_name" class="m-form-input"
                           placeholder="BCA / BRI / DANA / OVO" value="{{ old('bank_name') }}" maxlength="100" required>
                </div>

                <div style="display:grid; grid-template-columns: 1.618fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-3);">
                    <div>
                        <label class="m-form-label">No. Rekening</label>
                        <input type="text" name="account_number" class="m-form-input"
                               placeholder="1234567890" value="{{ old('account_number') }}" maxlength="50" required>
                    </div>
                    <div>
                        <label class="m-form-label">A.N.</label>
                        <input type="text" name="account_name" class="m-form-input"
                               placeholder="Nama" value="{{ old('account_name') }}" maxlength="100" required>
                    </div>
                </div>

                <button type="submit" class="m-btn-primary m-btn-primary-block">
                    <i class="bi bi-send-fill"></i> Kirim Permintaan
                </button>
            </form>
        </div>
    </div>

    <h2 class="m-section-title">Riwayat Penarikan ({{ $withdrawals->count() }})</h2>

    @if($withdrawals->isEmpty())
        <div class="m-empty">
            <i class="bi bi-clipboard-x m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Riwayat</h3>
            <p class="m-empty-text">Permintaan penarikan akan muncul di sini.</p>
        </div>
    @else
        @foreach($withdrawals as $w)
            @php
                $statusColor = match($w->status) {
                    'pending'  => ['#fef3c7', '#92400e', 'bi-clock-fill', 'Menunggu'],
                    'approved' => ['#d1fae5', '#065f46', 'bi-check-circle-fill', 'Disetujui'],
                    'rejected' => ['#fee2e2', '#991b1b', 'bi-x-circle-fill', 'Ditolak'],
                    default    => ['#f1f5f9', '#475569', 'bi-question-circle', ucfirst($w->status)],
                };
            @endphp
            <div class="m-card">
                <div class="m-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--fib-2);">
                        <div>
                            <div style="font-weight:800; color:var(--ink); font-size:var(--t-md);">Rp {{ number_format($w->nominal, 0, ',', '.') }}</div>
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ \Carbon\Carbon::parse($w->created_at)->translatedFormat('d M Y, H:i') }}</div>
                        </div>
                        <span style="background:{{ $statusColor[0] }}; color:{{ $statusColor[1] }}; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                            <i class="bi {{ $statusColor[2] }}"></i> {{ $statusColor[3] }}
                        </span>
                    </div>
                    <div style="display:flex; align-items:center; gap:var(--fib-2); padding:var(--fib-2); background:var(--mitra-blue-tint); border-radius:var(--r-sm); font-size:var(--t-xs);">
                        <i class="bi bi-bank" style="color:var(--mitra-blue);"></i>
                        <div>
                            <div style="font-weight:700; color:var(--ink); font-size:var(--t-xs);">{{ $w->bank_name }}</div>
                            <div style="color:var(--ink-soft); font-size:var(--t-xxs);">{{ $w->account_number }} a.n. {{ $w->account_name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="m-alert m-alert-info" style="margin-top:var(--fib-3);">
        <i class="bi bi-info-circle-fill"></i>
        <div>Saldo dipotong saat permintaan dikirim. Jika ditolak admin, saldo otomatis dikembalikan.</div>
    </div>
</div>
@endsection
