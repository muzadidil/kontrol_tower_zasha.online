@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Order Inden</div>
            <h1 class="m-hero-title">Booking Terjadwal</h1>
        </div>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-3);">Order yang dibooking pelanggan untuk dikerjakan di tanggal tertentu</div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif

    <div class="m-chip-bar">
        @php
            $tabs = [
                'menunggu_mitra' => ['Approval', $counts['menunggu_mitra'] ?? 0, '#f59e0b'],
                'aktif'          => ['Aktif',    $counts['aktif'] ?? 0,          'var(--mitra-blue)'],
                'riwayat'        => ['Riwayat',  null,                            'var(--ink-soft)'],
            ];
        @endphp
        @foreach($tabs as $key => [$label, $count, $color])
            @php $active = $filter === $key; @endphp
            <a href="{{ route('mitra.inden.index', ['status' => $key]) }}"
               class="m-chip {{ $active ? 'active' : '' }}"
               style="{{ $active ? 'background:'.$color.'; border-color:'.$color.';' : 'color:'.$color.'; border-color:'.$color.';' }}">
                {{ $label }}
                @if($count !== null && $count > 0)
                    <span class="m-chip-count" style="{{ $active ? 'background:rgba(255,255,255,0.25); color:#fff;' : 'background:'.$color.'; color:#fff;' }}">{{ $count }}</span>
                @endif
            </a>
        @endforeach
    </div>

    @if($orders->isEmpty())
        <div class="m-empty">
            <i class="bi bi-calendar-event m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Order Inden</h3>
            <p class="m-empty-text">
                @if($filter === 'menunggu_mitra')Tidak ada order yang butuh approval.
                @elseif($filter === 'aktif')Tidak ada order yang sedang berjalan.
                @else Belum ada riwayat order inden.
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
            <a href="{{ route('mitra.inden.show', $o->id) }}" style="text-decoration:none; color:inherit;">
                <div class="m-card">
                    <div class="m-card-body">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--fib-2); flex-wrap:wrap; gap:var(--fib-1);">
                            <div>
                                <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">{{ $nama }}</div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">#{{ $o->order_code ?? $o->id }}</div>
                            </div>
                            <span style="background:{{ $statusColor[0] }}; color:{{ $statusColor[1] }}; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div style="display:flex; align-items:center; gap:var(--fib-2); padding:var(--fib-2); background:var(--mitra-blue-tint); border-radius:var(--r-sm); margin-bottom:var(--fib-2);">
                            <i class="bi bi-calendar-event" style="color:var(--mitra-blue);"></i>
                            <div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Tanggal Pelaksanaan</div>
                                <div style="font-weight:700; color:var(--ink); font-size:var(--t-xs);">
                                    {{ \Carbon\Carbon::parse($o->tanggal_pelaksanaan)->translatedFormat('l, d M Y') }}
                                </div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                            <div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Pendapatan Anda</div>
                                <div style="font-weight:800; color:#16a34a; font-size:var(--t-sm);">Rp {{ number_format($o->pendapatan_mitra ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $o->durasi }} {{ $o->tipe_durasi }}</div>
                        </div>

                        @if($o->status === \App\Enums\IndenOrderStatus::MenungguMitra)
                            <div class="m-alert m-alert-warn" style="margin-top:var(--fib-2); margin-bottom:0;">
                                <i class="bi bi-exclamation-circle-fill"></i> Butuh approval Anda
                            </div>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    @endif
</div>
@endsection
