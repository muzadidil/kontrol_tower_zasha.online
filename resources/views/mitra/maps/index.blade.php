@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">PETA</div>
            <h5 class="fw-bold m-0">Lokasi Order Aktif</h5>
        </div>
    </div>
    <div class="text-center" style="font-size:.75rem;opacity:.85;">
        {{ $orders->count() }} order aktif dengan lokasi
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-geo-alt" style="font-size:3rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Tidak Ada Order Aktif</h6>
            <p class="text-muted small mb-0">
                Order yang sedang berjalan dengan lokasi pelanggan akan muncul di sini.
            </p>
        </div>
    @else
        @foreach($orders as $o)
            @php
                $statusColor = match($o->status) {
                    'menuju_lokasi' => ['#dbeafe', '#1e40af', 'bi-geo-fill',          'Menuju Lokasi'],
                    'di_lokasi'     => ['#d1fae5', '#065f46', 'bi-pin-map-fill',      'Di Lokasi'],
                    'dikerjakan'    => ['#fef3c7', '#92400e', 'bi-tools',             'Dikerjakan'],
                    'accepted'      => ['#e0e7ff', '#3730a3', 'bi-check-circle',      'Diterima'],
                    'belum_selesai' => ['#fee2e2', '#991b1b', 'bi-exclamation-circle','Belum Selesai'],
                    default         => ['#f1f5f9', '#475569', 'bi-circle',            ucfirst($o->status)],
                };
                $nama = $o->pelanggan->nama_panggilan ?? $o->pelanggan->nama_pelanggan ?? 'Pelanggan';
                $orderTypeLabel = ucfirst($o->order_type);

                // Deep links
                $gmapsNav  = "https://www.google.com/maps/dir/?api=1&destination={$o->lat},{$o->lng}&travelmode=driving";
                $gmapsView = "https://www.google.com/maps?q={$o->lat},{$o->lng}";
                $waLink    = $o->pelanggan && $o->pelanggan->no_wa
                    ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $o->pelanggan->no_wa)
                    : null;
            @endphp

            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-1">
                        <div>
                            <div class="fw-bold small" style="color:#1e293b;">{{ $nama }}</div>
                            <small class="text-muted" style="font-size:.65rem;">
                                <span class="badge bg-light text-dark" style="font-size:.55rem;">{{ $orderTypeLabel }}</span>
                                · #{{ $o->tracking_id }}
                            </small>
                        </div>
                        <span class="badge rounded-pill px-3 py-1"
                              style="background:{{ $statusColor[0] }};color:{{ $statusColor[1] }};font-size:.65rem;">
                            <i class="bi {{ $statusColor[2] }} me-1"></i>{{ $statusColor[3] }}
                        </span>
                    </div>

                    {{-- Alamat --}}
                    <div class="d-flex align-items-start gap-2 p-2 rounded-3 mb-2"
                         style="background:#f8fafc;">
                        <i class="bi bi-geo-alt-fill" style="color:#ef4444;font-size:1rem;flex-shrink:0;margin-top:2px;"></i>
                        <div class="flex-grow-1 min-w-0">
                            <small class="text-muted" style="font-size:.6rem;">Alamat tujuan</small>
                            <div class="small" style="color:#1e293b;">{{ $o->alamat }}</div>
                            @if($o->multi_stop && $o->stops_count > 0)
                                <span class="badge bg-info text-white mt-1" style="font-size:.6rem;">
                                    <i class="bi bi-signpost-split"></i> {{ $o->stops_count }} stops
                                </span>
                            @endif
                            <small class="text-muted d-block mt-1" style="font-size:.6rem;">
                                Koordinat: {{ number_format($o->lat, 6) }}, {{ number_format($o->lng, 6) }}
                            </small>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div class="d-grid gap-2">
                        {{-- Navigasi Google Maps (deep link) --}}
                        <a href="{{ $gmapsNav }}" target="_blank"
                           class="btn btn-primary rounded-pill py-2 fw-bold"
                           style="background:#1a73e8;border-color:#1a73e8;">
                            <i class="bi bi-navigation-fill me-1"></i>Navigasi (Google Maps)
                        </a>

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ $gmapsView }}" target="_blank"
                                   class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                    <i class="bi bi-map"></i> Lihat Peta
                                </a>
                            </div>
                            <div class="col-6">
                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank"
                                       class="btn btn-sm rounded-pill w-100"
                                       style="background:#25d366;color:#fff;border:none;">
                                        <i class="bi bi-whatsapp"></i> Chat
                                    </a>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill w-100" disabled>
                                        <i class="bi bi-whatsapp"></i> Chat
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="alert alert-info rounded-3 small mt-3 mb-0"
             style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;font-size:.75rem;">
            <i class="bi bi-info-circle-fill me-1"></i>
            <strong>Tips:</strong> Tombol <strong>Navigasi</strong> akan buka Google Maps dengan rute langsung
            dari posisi Anda ke lokasi pelanggan.
        </div>
    @endif
</div>
@endsection
