@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">LAPORAN</div>
            <h5 class="fw-bold m-0">Penghasilan Harian</h5>
        </div>
        <a href="{{ route('mitra.laporan.bulanan') }}"
           class="ms-auto text-white text-decoration-none small fw-bold"
           style="background:rgba(255,255,255,.18);padding:6px 12px;border-radius:999px;">
            Bulanan <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    {{-- Total Bersih --}}
    <div class="text-center">
        <div style="font-size:.7rem;opacity:.8;">PENGHASILAN BERSIH</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:-.02em;">
            Rp {{ number_format($stats['total_bersih'], 0, ',', '.') }}
        </div>
        <div style="font-size:.7rem;opacity:.7;">
            dari {{ $stats['total_order'] }} order selesai · {{ $tanggal->translatedFormat('l, d M Y') }}
        </div>
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    {{-- Date Nav (7 hari) --}}
    <div class="d-flex gap-1 mb-3 overflow-auto pb-2" style="scrollbar-width:thin;">
        @foreach($rangeHari as $d)
            @php $isActive = $d->isSameDay($tanggal); @endphp
            <a href="{{ route('mitra.laporan.harian', ['tanggal' => $d->format('Y-m-d')]) }}"
               class="text-decoration-none flex-shrink-0"
               style="min-width:62px;padding:10px 8px;border-radius:12px;text-align:center;
                      background:{{ $isActive ? '#005aa9' : '#fff' }};
                      color:{{ $isActive ? '#fff' : '#1e293b' }};
                      box-shadow:0 1px 2px rgba(0,0,0,.04);">
                <div style="font-size:.6rem;opacity:.7;text-transform:uppercase;">{{ $d->translatedFormat('D') }}</div>
                <div style="font-size:1.1rem;font-weight:700;">{{ $d->format('d') }}</div>
                <div style="font-size:.6rem;opacity:.7;">{{ $d->translatedFormat('M') }}</div>
            </a>
        @endforeach
    </div>

    {{-- Stats Grid --}}
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-cash-coin" style="color:#16a34a;"></i>
                    <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Omset</small>
                </div>
                <div class="fw-bold" style="color:#1e293b;font-size:1.05rem;">
                    Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-percent" style="color:#dc2626;"></i>
                    <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Komisi Zasha</small>
                </div>
                <div class="fw-bold" style="color:#1e293b;font-size:1.05rem;">
                    Rp {{ number_format($stats['total_komisi'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Order --}}
    <h6 class="fw-bold mt-3 mb-2" style="color:#1e293b;">
        Order Selesai Hari Ini ({{ $orders->count() }})
    </h6>

    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-moon-stars" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Order Selesai</h6>
            <p class="text-muted small mb-0">Pada {{ $tanggal->translatedFormat('d M Y') }} belum ada order yang selesai.</p>
        </div>
    @else
        @foreach($orders as $o)
            @php
                $bersih = $o->harga_jual - $o->komisi_zasha;
                $nama = $o->pelanggan_panggilan ?? $o->nama_pelanggan ?? 'Pelanggan';
                $typeLabel = ucfirst(str_replace(['order_', '_'], ['', ' '], $o->order_type));
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body py-3">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <div class="fw-bold small" style="color:#1e293b;">{{ $nama }}</div>
                            <small class="text-muted">
                                <span class="badge bg-light text-dark" style="font-size:.6rem;">{{ $typeLabel }}</span>
                                · {{ \Carbon\Carbon::parse($o->updated_at)->translatedFormat('H:i') }}
                            </small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color:#16a34a;">
                                +Rp {{ number_format($bersih, 0, ',', '.') }}
                            </div>
                            <small class="text-muted" style="font-size:.6rem;">
                                omset Rp {{ number_format($o->harga_jual, 0, ',', '.') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
