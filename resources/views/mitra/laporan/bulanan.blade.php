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
            <h5 class="fw-bold m-0">Penghasilan Bulanan</h5>
        </div>
        <a href="{{ route('mitra.laporan.harian') }}"
           class="ms-auto text-white text-decoration-none small fw-bold"
           style="background:rgba(255,255,255,.18);padding:6px 12px;border-radius:999px;">
            <i class="bi bi-arrow-left me-1"></i> Harian
        </a>
    </div>

    {{-- Total Bersih --}}
    <div class="text-center">
        <div style="font-size:.7rem;opacity:.8;">PENGHASILAN BERSIH</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:-.02em;">
            Rp {{ number_format($stats['total_bersih'], 0, ',', '.') }}
        </div>
        <div style="font-size:.7rem;opacity:.7;">
            {{ $bulan->translatedFormat('F Y') }} · {{ $stats['total_order'] }} order selesai
        </div>
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    {{-- Month Nav (6 bulan) --}}
    <div class="d-flex gap-1 mb-3 overflow-auto pb-2" style="scrollbar-width:thin;">
        @foreach($rangeBulan as $m)
            @php $isActive = $m->isSameMonth($bulan); @endphp
            <a href="{{ route('mitra.laporan.bulanan', ['bulan' => $m->format('Y-m')]) }}"
               class="text-decoration-none flex-shrink-0"
               style="min-width:75px;padding:10px 12px;border-radius:12px;text-align:center;
                      background:{{ $isActive ? '#005aa9' : '#fff' }};
                      color:{{ $isActive ? '#fff' : '#1e293b' }};
                      box-shadow:0 1px 2px rgba(0,0,0,.04);">
                <div style="font-size:.65rem;opacity:.7;">{{ $m->format('Y') }}</div>
                <div style="font-size:.9rem;font-weight:700;">{{ $m->translatedFormat('M') }}</div>
            </a>
        @endforeach
    </div>

    {{-- Stats Grid --}}
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-cash-coin" style="color:#16a34a;"></i>
                    <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Total Omset</small>
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
                    <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Total Komisi</small>
                </div>
                <div class="fw-bold" style="color:#1e293b;font-size:1.05rem;">
                    Rp {{ number_format($stats['total_komisi'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-receipt" style="color:#005aa9;"></i>
                    <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Rata per Order</small>
                </div>
                <div class="fw-bold" style="color:#1e293b;font-size:1.05rem;">
                    Rp {{ number_format($stats['rata_per_order'], 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-bar-chart-fill" style="color:#0078d4;"></i>
                    <small class="text-muted fw-bold text-uppercase" style="font-size:.65rem;">Hari Aktif</small>
                </div>
                <div class="fw-bold" style="color:#1e293b;font-size:1.05rem;">
                    {{ $perHari->count() }} hari
                </div>
            </div>
        </div>
    </div>

    {{-- Breakdown per Jenis --}}
    @if($perJenis->isNotEmpty())
        <h6 class="fw-bold mt-3 mb-2" style="color:#1e293b;">Pendapatan per Jenis Order</h6>
        @php $totalOmset = $perJenis->sum('omset'); @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3">
                @foreach($perJenis as $j)
                    @php
                        $pct = $totalOmset > 0 ? round(($j->omset / $totalOmset) * 100) : 0;
                        $typeLabel = ucfirst(str_replace(['order_', '_'], ['', ' '], $j->order_type));
                    @endphp
                    <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <span class="fw-semibold" style="color:#1e293b;font-size:.85rem;">{{ $typeLabel }}</span>
                                <small class="text-muted ms-1">({{ $j->jumlah }} order)</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color:#16a34a;font-size:.85rem;">
                                    +Rp {{ number_format($j->bersih, 0, ',', '.') }}
                                </div>
                                <small class="text-muted" style="font-size:.65rem;">
                                    omset Rp {{ number_format($j->omset, 0, ',', '.') }}
                                </small>
                            </div>
                        </div>
                        <div style="height:6px;background:#eff6ff;border-radius:999px;overflow:hidden;">
                            <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#005aa9,#0078d4);"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Breakdown per Hari --}}
    <h6 class="fw-bold mt-3 mb-2" style="color:#1e293b;">Penghasilan per Hari</h6>

    @if($perHari->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-calendar-x" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Order Selesai</h6>
            <p class="text-muted small mb-0">Bulan {{ $bulan->translatedFormat('F Y') }} belum ada penghasilan.</p>
        </div>
    @else
        @php $maxOmset = $perHari->max('omset'); @endphp
        <div class="card border-0 shadow-sm rounded-4">
            @foreach($perHari as $h)
                @php $pct = $maxOmset > 0 ? round(($h->omset / $maxOmset) * 100) : 0; @endphp
                <a href="{{ route('mitra.laporan.harian', ['tanggal' => $h->tanggal->format('Y-m-d')]) }}"
                   class="text-decoration-none">
                    <div class="d-flex align-items-center gap-3 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}"
                         style="color:#1e293b;">
                        <div style="min-width:50px;">
                            <div class="fw-bold" style="font-size:1.1rem;color:#005aa9;">{{ $h->tanggal->format('d') }}</div>
                            <small class="text-muted" style="font-size:.65rem;">{{ $h->tanggal->translatedFormat('D') }}</small>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">{{ $h->jumlah }} order</small>
                                <div class="fw-bold small" style="color:#16a34a;">
                                    +Rp {{ number_format($h->bersih, 0, ',', '.') }}
                                </div>
                            </div>
                            <div style="height:4px;background:#f1f5f9;border-radius:999px;overflow:hidden;">
                                <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#005aa9,#0078d4);"></div>
                            </div>
                        </div>
                        <i class="bi bi-chevron-right text-muted" style="font-size:.7rem;"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
