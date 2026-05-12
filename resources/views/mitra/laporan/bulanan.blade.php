@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Laporan</div>
            <h1 class="m-hero-title">Penghasilan Bulanan</h1>
        </div>
        <a href="{{ route('mitra.laporan.harian') }}" class="m-hero-action">
            <i class="bi bi-arrow-left"></i> Harian
        </a>
    </div>

    <div style="text-align:center;">
        <div class="m-hero-stat-label">PENGHASILAN BERSIH</div>
        <div class="m-hero-stat-value">Rp {{ number_format($stats['total_bersih'], 0, ',', '.') }}</div>
        <div class="m-hero-stat-sub">{{ $bulan->translatedFormat('F Y') }} · {{ $stats['total_order'] }} order selesai</div>
    </div>
</div>

<div class="m-page">
    {{-- Month Nav --}}
    <div class="m-chip-bar">
        @foreach($rangeBulan as $m)
            @php $isActive = $m->isSameMonth($bulan); @endphp
            <a href="{{ route('mitra.laporan.bulanan', ['bulan' => $m->format('Y-m')]) }}"
               class="m-chip {{ $isActive ? 'active' : '' }}"
               style="flex-direction:column; min-width:var(--fib-7); padding:var(--fib-2);">
                <span style="font-size:var(--t-xxs); opacity:0.7;">{{ $m->format('Y') }}</span>
                <span style="font-size:var(--t-sm); font-weight:700;">{{ $m->translatedFormat('M') }}</span>
            </a>
        @endforeach
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-3);">
        <div class="m-stat-card">
            <div class="m-stat-card-label"><i class="bi bi-cash-coin" style="color:#16a34a;"></i> Total Omset</div>
            <div class="m-stat-card-value">Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}</div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-card-label"><i class="bi bi-percent" style="color:#dc2626;"></i> Total Komisi</div>
            <div class="m-stat-card-value">Rp {{ number_format($stats['total_komisi'], 0, ',', '.') }}</div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-card-label"><i class="bi bi-receipt" style="color:var(--mitra-blue);"></i> Rata per Order</div>
            <div class="m-stat-card-value">Rp {{ number_format($stats['rata_per_order'], 0, ',', '.') }}</div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-card-label"><i class="bi bi-bar-chart-fill" style="color:var(--mitra-blue-mid);"></i> Hari Aktif</div>
            <div class="m-stat-card-value">{{ $perHari->count() }} hari</div>
        </div>
    </div>

    @if($perJenis->isNotEmpty())
        <h2 class="m-section-title">Pendapatan per Jenis Order</h2>
        @php $totalOmset = $perJenis->sum('omset'); @endphp
        <div class="m-card">
            <div class="m-card-body">
                @foreach($perJenis as $j)
                    @php
                        $pct = $totalOmset > 0 ? round(($j->omset / $totalOmset) * 100) : 0;
                        $typeLabel = ucfirst(str_replace(['order_', '_'], ['', ' '], $j->order_type));
                    @endphp
                    <div style="margin-bottom:var(--fib-3); {{ !$loop->last ? 'padding-bottom:var(--fib-3); border-bottom:1px solid var(--line);' : 'margin-bottom:0;' }}">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--fib-1);">
                            <div>
                                <span style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">{{ $typeLabel }}</span>
                                <span style="color:var(--ink-soft); font-size:var(--t-xxs);"> ({{ $j->jumlah }} order)</span>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:800; color:#16a34a; font-size:var(--t-sm);">+Rp {{ number_format($j->bersih, 0, ',', '.') }}</div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">omset Rp {{ number_format($j->omset, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div style="height:var(--fib-1); background:var(--mitra-blue-tint); border-radius:var(--r-pill); overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg,var(--mitra-blue),var(--mitra-blue-mid));"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <h2 class="m-section-title">Penghasilan per Hari</h2>

    @if($perHari->isEmpty())
        <div class="m-empty">
            <i class="bi bi-calendar-x m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Order Selesai</h3>
            <p class="m-empty-text">Bulan {{ $bulan->translatedFormat('F Y') }} belum ada penghasilan.</p>
        </div>
    @else
        @php $maxOmset = $perHari->max('omset'); @endphp
        <div class="m-card">
            @foreach($perHari as $h)
                @php $pct = $maxOmset > 0 ? round(($h->omset / $maxOmset) * 100) : 0; @endphp
                <a href="{{ route('mitra.laporan.harian', ['tanggal' => $h->tanggal->format('Y-m-d')]) }}"
                   style="text-decoration:none; color:var(--ink);
                          display:flex; align-items:center; gap:var(--fib-3);
                          padding:var(--fib-2) var(--fib-3);
                          {{ !$loop->last ? 'border-bottom:1px solid var(--line);' : '' }}">
                    <div style="min-width:var(--fib-6);">
                        <div style="font-weight:800; font-size:var(--t-md); color:var(--mitra-blue); line-height:1;">{{ $h->tanggal->format('d') }}</div>
                        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $h->tanggal->translatedFormat('D') }}</div>
                    </div>
                    <div style="flex-grow:1;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--fib-1);">
                            <span style="font-size:var(--t-xs); color:var(--ink-soft);">{{ $h->jumlah }} order</span>
                            <span style="font-weight:700; color:#16a34a; font-size:var(--t-xs);">+Rp {{ number_format($h->bersih, 0, ',', '.') }}</span>
                        </div>
                        <div style="height:3px; background:var(--line); border-radius:var(--r-pill); overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg,var(--mitra-blue),var(--mitra-blue-mid));"></div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right" style="color:var(--ink-soft); font-size:var(--t-xxs);"></i>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
