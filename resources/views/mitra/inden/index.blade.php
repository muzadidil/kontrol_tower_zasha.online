@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">ORDER INDEN</div>
            <h5 class="fw-bold m-0">Booking Terjadwal</h5>
        </div>
    </div>
    <div class="text-center" style="font-size:.75rem;opacity:.85;">
        Order yang dibooking pelanggan untuk dikerjakan di tanggal tertentu
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm small">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="d-flex gap-2 mb-3 overflow-auto pb-2" style="scrollbar-width:thin;">
        @php
            $tabs = [
                'menunggu_mitra' => ['Approval', $counts['menunggu_mitra'] ?? 0, '#f59e0b'],
                'aktif'          => ['Aktif',    $counts['aktif'] ?? 0,          '#005aa9'],
                'riwayat'        => ['Riwayat',  null,                            '#64748b'],
            ];
        @endphp
        @foreach($tabs as $key => [$label, $count, $color])
            @php $active = $filter === $key; @endphp
            <a href="{{ route('mitra.inden.index', ['status' => $key]) }}"
               class="text-decoration-none flex-shrink-0 d-flex align-items-center gap-1"
               style="padding:6px 14px;border-radius:999px;font-size:.75rem;font-weight:600;
                      white-space:nowrap;
                      background:{{ $active ? $color : '#fff' }};
                      color:{{ $active ? '#fff' : $color }};
                      border:1px solid {{ $color }};">
                <span>{{ $label }}</span>
                @if($count !== null && $count > 0)
                    <span style="background:{{ $active ? '#fff' : $color }};
                                 color:{{ $active ? $color : '#fff' }};
                                 padding:1px 6px;border-radius:999px;font-size:.65rem;">
                        {{ $count }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Daftar Order --}}
    @if($orders->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-calendar-event" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Order Inden</h6>
            <p class="text-muted small mb-0">
                @if($filter === 'menunggu_mitra')
                    Tidak ada order yang butuh approval.
                @elseif($filter === 'aktif')
                    Tidak ada order yang sedang berjalan.
                @else
                    Belum ada riwayat order inden.
                @endif
            </p>
        </div>
    @else
        @foreach($orders as $o)
            @php
                $statusLabel = $o->status instanceof \App\Enums\IndenOrderStatus ? $o->status->label() : $o->status;
                $statusColor = match(true) {
                    $o->status === \App\Enums\IndenOrderStatus::MenungguMitra      => ['#fef3c7', '#92400e'],
                    $o->status === \App\Enums\IndenOrderStatus::Ditolak            => ['#fee2e2', '#991b1b'],
                    $o->status === \App\Enums\IndenOrderStatus::MenungguDp         => ['#fef3c7', '#92400e'],
                    $o->status === \App\Enums\IndenOrderStatus::DpDibayar          => ['#dbeafe', '#1e40af'],
                    $o->status === \App\Enums\IndenOrderStatus::Dikerjakan         => ['#dbeafe', '#1e40af'],
                    $o->status === \App\Enums\IndenOrderStatus::MenungguPelunasan  => ['#fef3c7', '#92400e'],
                    $o->status === \App\Enums\IndenOrderStatus::Selesai            => ['#d1fae5', '#065f46'],
                    default                                                       => ['#f1f5f9', '#475569'],
                };
                $nama = $o->pelanggan->nama_panggilan ?? $o->pelanggan->nama_pelanggan ?? 'Pelanggan';
            @endphp
            <a href="{{ route('mitra.inden.show', $o->id) }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm rounded-4 mb-2" style="color:#1e293b;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-1">
                            <div>
                                <div class="fw-bold small">{{ $nama }}</div>
                                <small class="text-muted" style="font-size:.65rem;">
                                    #{{ $o->order_code ?? $o->id }}
                                </small>
                            </div>
                            <span class="badge rounded-pill px-3 py-1"
                                  style="background:{{ $statusColor[0] }};color:{{ $statusColor[1] }};font-size:.65rem;">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3" style="background:#f8fafc;">
                            <i class="bi bi-calendar-event" style="color:#005aa9;"></i>
                            <div>
                                <small class="text-muted" style="font-size:.65rem;">Tanggal Pelaksanaan</small>
                                <div class="fw-bold small">
                                    {{ \Carbon\Carbon::parse($o->tanggal_pelaksanaan)->translatedFormat('l, d M Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-end">
                            <div>
                                <small class="text-muted" style="font-size:.65rem;">Pendapatan Anda</small>
                                <div class="fw-bold" style="color:#16a34a;">
                                    Rp {{ number_format($o->pendapatan_mitra ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <small class="text-muted" style="font-size:.65rem;">
                                {{ $o->durasi }} {{ $o->tipe_durasi }}
                            </small>
                        </div>

                        @if($o->status === \App\Enums\IndenOrderStatus::MenungguMitra)
                            <div class="alert alert-warning rounded-3 mt-2 mb-0 py-2 px-3" style="font-size:.75rem;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i>
                                Butuh approval Anda
                            </div>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    @endif
</div>
@endsection
