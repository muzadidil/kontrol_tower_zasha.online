@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Peta</div>
            <h1 class="m-hero-title">Lokasi Order Aktif</h1>
        </div>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-2);">{{ $orders->count() }} order aktif dengan lokasi</div>
</div>

<div class="m-page">
    @if($orders->isEmpty())
        <div class="m-empty">
            <i class="bi bi-geo-alt m-empty-icon"></i>
            <h3 class="m-empty-title">Tidak Ada Order Aktif</h3>
            <p class="m-empty-text">Order yang sedang berjalan dengan lokasi pelanggan akan muncul di sini.</p>
        </div>
    @else
        @foreach($orders as $o)
            @php
                $statusColor = match($o->status) {
                    'menuju_lokasi' => ['#dbeafe', '#1e40af', 'bi-geo-fill', 'Menuju'],
                    'di_lokasi'     => ['#d1fae5', '#065f46', 'bi-pin-map-fill', 'Di Lokasi'],
                    'dikerjakan'    => ['#fef3c7', '#92400e', 'bi-tools', 'Dikerjakan'],
                    'accepted'      => ['#e0e7ff', '#3730a3', 'bi-check-circle', 'Diterima'],
                    'belum_selesai' => ['#fee2e2', '#991b1b', 'bi-exclamation-circle', 'Belum'],
                    default         => ['#f1f5f9', '#475569', 'bi-circle', ucfirst($o->status)],
                };
                $nama = $o->pelanggan->nama_panggilan ?? $o->pelanggan->nama_pelanggan ?? 'Pelanggan';
                $orderTypeLabel = ucfirst($o->order_type);
                $gmapsNav  = "https://www.google.com/maps/dir/?api=1&destination={$o->lat},{$o->lng}&travelmode=driving";
                $gmapsView = "https://www.google.com/maps?q={$o->lat},{$o->lng}";
                $waLink = $o->pelanggan && $o->pelanggan->no_wa
                    ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $o->pelanggan->no_wa)
                    : null;
            @endphp

            <div class="m-card">
                <div class="m-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:var(--fib-1); margin-bottom:var(--fib-2);">
                        <div>
                            <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">{{ $nama }}</div>
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft);">
                                <span style="background:var(--mitra-blue-tint); color:var(--mitra-blue); padding:1px var(--fib-1); border-radius:var(--r-sm);">{{ $orderTypeLabel }}</span>
                                · #{{ $o->tracking_id }}
                            </div>
                        </div>
                        <span style="background:{{ $statusColor[0] }}; color:{{ $statusColor[1] }}; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                            <i class="bi {{ $statusColor[2] }}"></i> {{ $statusColor[3] }}
                        </span>
                    </div>

                    <div style="display:flex; align-items:flex-start; gap:var(--fib-2); padding:var(--fib-2) var(--fib-3); background:var(--mitra-blue-tint); border-radius:var(--r-md); margin-bottom:var(--fib-2);">
                        <i class="bi bi-geo-alt-fill" style="color:#ef4444; font-size:var(--t-md); flex-shrink:0; margin-top:2px;"></i>
                        <div style="flex-grow:1; min-width:0;">
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Alamat tujuan</div>
                            <div style="font-size:var(--t-xs); color:var(--ink);">{{ $o->alamat }}</div>
                            @if($o->multi_stop && $o->stops_count > 0)
                                <span style="background:var(--mitra-blue-mid); color:#fff; padding:1px var(--fib-1); border-radius:var(--r-sm); font-size:var(--t-xxs); margin-top:var(--fib-1); display:inline-block;">
                                    <i class="bi bi-signpost-split"></i> {{ $o->stops_count }} stops
                                </span>
                            @endif
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft); margin-top:var(--fib-1);">
                                Koordinat: {{ number_format($o->lat, 6) }}, {{ number_format($o->lng, 6) }}
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:var(--fib-2);">
                        <a href="{{ $gmapsNav }}" target="_blank"
                           class="m-btn-primary m-btn-primary-block"
                           style="background:linear-gradient(135deg,#1a73e8,#1557b0);">
                            <i class="bi bi-navigation-fill"></i> Navigasi (Google Maps)
                        </a>
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:var(--fib-1);">
                            <a href="{{ $gmapsView }}" target="_blank" class="m-chip" style="justify-content:center; border-color:var(--mitra-blue); color:var(--mitra-blue);">
                                <i class="bi bi-map"></i> Lihat Peta
                            </a>
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank" class="m-chip" style="justify-content:center; background:#25d366; color:#fff; border-color:#25d366;">
                                    <i class="bi bi-whatsapp"></i> Chat
                                </a>
                            @else
                                <span class="m-chip" style="justify-content:center; opacity:0.5;">
                                    <i class="bi bi-whatsapp"></i> Chat
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="m-alert m-alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                <strong>Tips:</strong> Tombol <strong>Navigasi</strong> akan buka Google Maps dengan rute langsung dari posisi Anda ke lokasi pelanggan.
            </div>
        </div>
    @endif
</div>
@endsection
