@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Laporan</div>
            <h1 class="m-hero-title">Penghasilan Harian</h1>
        </div>
        <a href="{{ route('mitra.laporan.bulanan') }}" class="m-hero-action">
            Bulanan <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div style="text-align:center;">
        <div class="m-hero-stat-label">PENGHASILAN BERSIH</div>
        <div class="m-hero-stat-value">Rp {{ number_format($stats['total_bersih'], 0, ',', '.') }}</div>
        <div class="m-hero-stat-sub">
            dari {{ $stats['total_order'] }} order selesai · {{ $tanggal->translatedFormat('l, d M Y') }}
        </div>
    </div>
</div>

<div class="m-page">
    {{-- Date Nav (7 hari, fibonacci spacing) --}}
    <div class="m-chip-bar">
        @foreach($rangeHari as $d)
            @php $isActive = $d->isSameDay($tanggal); @endphp
            <a href="{{ route('mitra.laporan.harian', ['tanggal' => $d->format('Y-m-d')]) }}"
               class="m-chip {{ $isActive ? 'active' : '' }}"
               style="flex-direction:column; min-width:var(--fib-7); padding:var(--fib-2);">
                <span style="font-size:var(--t-xxs); opacity:0.7; text-transform:uppercase;">{{ $d->translatedFormat('D') }}</span>
                <span style="font-size:var(--t-md); font-weight:800;">{{ $d->format('d') }}</span>
                <span style="font-size:var(--t-xxs); opacity:0.7;">{{ $d->translatedFormat('M') }}</span>
            </a>
        @endforeach
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-3);">
        <div class="m-stat-card">
            <div class="m-stat-card-label"><i class="bi bi-cash-coin" style="color:#16a34a;"></i> Omset</div>
            <div class="m-stat-card-value">Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}</div>
        </div>
        <div class="m-stat-card">
            <div class="m-stat-card-label"><i class="bi bi-percent" style="color:#dc2626;"></i> Komisi Zasha</div>
            <div class="m-stat-card-value">Rp {{ number_format($stats['total_komisi'], 0, ',', '.') }}</div>
        </div>
    </div>

    <h2 class="m-section-title">Order Selesai Hari Ini ({{ $orders->count() }})</h2>

    @if($orders->isEmpty())
        <div class="m-empty">
            <i class="bi bi-moon-stars m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Order Selesai</h3>
            <p class="m-empty-text">Pada {{ $tanggal->translatedFormat('d M Y') }} belum ada order yang selesai.</p>
        </div>
    @else
        @foreach($orders as $o)
            @php
                $bersih = $o->harga_jual - $o->komisi_zasha;
                $nama = $o->pelanggan_panggilan ?? $o->nama_pelanggan ?? 'Pelanggan';
                $typeLabel = ucfirst(str_replace(['order_', '_'], ['', ' '], $o->order_type));
            @endphp
            <div class="m-card">
                <div class="m-card-body" style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">{{ $nama }}</div>
                        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">
                            <span style="background:var(--mitra-blue-tint); color:var(--mitra-blue); padding:1px var(--fib-1); border-radius:var(--r-sm);">{{ $typeLabel }}</span>
                            · {{ \Carbon\Carbon::parse($o->updated_at)->translatedFormat('H:i') }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:800; color:#16a34a; font-size:var(--t-sm);">+Rp {{ number_format($bersih, 0, ',', '.') }}</div>
                        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">omset Rp {{ number_format($o->harga_jual, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
