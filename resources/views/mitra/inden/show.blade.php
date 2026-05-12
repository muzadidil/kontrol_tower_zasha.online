@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

@php
    $statusLabel = $order->status instanceof \App\Enums\IndenOrderStatus ? $order->status->label() : $order->status;
    $isMenungguMitra = $order->status === \App\Enums\IndenOrderStatus::MenungguMitra;
    $isDpDibayar = $order->status === \App\Enums\IndenOrderStatus::DpDibayar;
    $isDikerjakan = $order->status === \App\Enums\IndenOrderStatus::Dikerjakan;
    $nama = $order->pelanggan->nama_panggilan ?? $order->pelanggan->nama_pelanggan ?? 'Pelanggan';
    $tanggal = \Carbon\Carbon::parse($order->tanggal_pelaksanaan);
@endphp

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.inden.index') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Order Inden</div>
            <h1 class="m-hero-title">#{{ $order->order_code ?? $order->id }}</h1>
        </div>
    </div>
    <div style="text-align:center;">
        <span style="background:rgba(255,255,255,0.2); color:#fff; padding:var(--fib-1) var(--fib-3); border-radius:var(--r-pill); font-size:var(--t-xs); font-weight:700;">{{ $statusLabel }}</span>
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif

    {{-- Tanggal Pelaksanaan (golden ratio prominent) --}}
    <div class="m-card" style="background:linear-gradient(135deg,var(--mitra-blue-tint),#fff);">
        <div class="m-card-body" style="text-align:center;">
            <div style="font-size:var(--t-xxs); color:var(--ink-soft); text-transform:uppercase; font-weight:700; letter-spacing:0.05em;">Tanggal Pelaksanaan</div>
            <div style="font-weight:800; color:var(--mitra-blue); font-size:var(--t-xl); margin-top:var(--fib-1);">{{ $tanggal->translatedFormat('d M Y') }}</div>
            <div style="font-size:var(--t-xs); color:var(--ink-soft);">{{ $tanggal->translatedFormat('l') }} · {{ $tanggal->diffForHumans() }}</div>
        </div>
    </div>

    {{-- Pelanggan --}}
    <div class="m-card">
        <div class="m-card-body" style="display:flex; align-items:center; gap:var(--fib-3);">
            <div style="width:var(--fib-6); height:var(--fib-6); border-radius:50%; background:var(--mitra-blue-soft); color:var(--mitra-blue); display:flex; align-items:center; justify-content:center; font-size:var(--t-lg); flex-shrink:0;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div style="flex-grow:1; min-width:0;">
                <div style="font-weight:700; color:var(--ink);">{{ $nama }}</div>
                @if($order->pelanggan && $order->pelanggan->no_wa)
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">
                        <i class="bi bi-whatsapp" style="color:#25d366;"></i> {{ $order->pelanggan->no_wa }}
                    </div>
                @endif
            </div>
            @if($order->pelanggan && $order->pelanggan->no_wa)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->pelanggan->no_wa) }}" target="_blank"
                   class="m-chip" style="background:#25d366; color:#fff; border-color:#25d366; flex-shrink:0;">
                    <i class="bi bi-whatsapp"></i> Chat
                </a>
            @endif
        </div>
    </div>

    {{-- Detail Order --}}
    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">Detail Order</h2>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:var(--fib-2);">
                <div>
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Durasi</div>
                    <div style="font-weight:700; font-size:var(--t-sm);">{{ $order->durasi }} {{ $order->tipe_durasi }}</div>
                </div>
                <div>
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Tarif</div>
                    <div style="font-weight:700; font-size:var(--t-sm);">Rp {{ number_format($order->tarif, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Total Biaya</div>
                    <div style="font-weight:700; font-size:var(--t-sm);">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Pendapatan Anda</div>
                    <div style="font-weight:700; font-size:var(--t-sm); color:#16a34a;">Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</div>
                </div>
            </div>
            @if($order->keterangan_kerja)
                <hr style="border-color:var(--line); margin:var(--fib-2) 0;">
                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Keterangan Pekerjaan</div>
                <div style="font-size:var(--t-xs);">{{ $order->keterangan_kerja }}</div>
            @endif
        </div>
    </div>

    {{-- Lokasi --}}
    @if($order->alamat_pelanggan)
        <div class="m-card">
            <div class="m-card-body">
                <h2 class="m-section-title" style="margin-top:0;"><i class="bi bi-geo-alt-fill" style="color:#ef4444;"></i> Lokasi</h2>
                <div style="font-size:var(--t-xs); color:var(--ink-soft);">{{ $order->alamat_pelanggan }}</div>
                @if($order->pelanggan_lat && $order->pelanggan_lng)
                    <a href="https://www.google.com/maps?q={{ $order->pelanggan_lat }},{{ $order->pelanggan_lng }}"
                       target="_blank" class="m-chip" style="margin-top:var(--fib-2); border-color:var(--mitra-blue); color:var(--mitra-blue);">
                        <i class="bi bi-map"></i> Buka Maps
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Pembayaran --}}
    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">Status Pembayaran</h2>
            <div style="display:flex; justify-content:space-between; align-items:center; padding:var(--fib-2) 0; border-bottom:1px solid var(--line);">
                <div>
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">DP</div>
                    <div style="font-weight:700; font-size:var(--t-sm);">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</div>
                </div>
                @if($order->dp_paid_at)
                    <span style="background:#d1fae5; color:#065f46; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;"><i class="bi bi-check-circle-fill"></i> Dibayar</span>
                @else
                    <span style="background:#fef3c7; color:#92400e; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;"><i class="bi bi-clock-fill"></i> Menunggu</span>
                @endif
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding-top:var(--fib-2);">
                <div>
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Pelunasan</div>
                    <div style="font-weight:700; font-size:var(--t-sm);">Rp {{ number_format($order->pelunasan_amount, 0, ',', '.') }}</div>
                </div>
                @if($order->pelunasan_paid_at)
                    <span style="background:#d1fae5; color:#065f46; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;"><i class="bi bi-check-circle-fill"></i> Dibayar</span>
                @else
                    <span style="background:var(--line); color:var(--ink-soft); padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;"><i class="bi bi-hourglass-split"></i> Belum</span>
                @endif
            </div>
        </div>
    </div>

    @if($order->alasan_penolakan)
        <div class="m-alert m-alert-error">
            <i class="bi bi-x-circle-fill"></i> <strong>Alasan Penolakan:</strong> {{ $order->alasan_penolakan }}
        </div>
    @endif

    {{-- Action Buttons --}}
    @if($isMenungguMitra)
        <div class="m-card">
            <div class="m-card-body">
                <h2 class="m-section-title" style="margin-top:0;">Konfirmasi Order</h2>
                <form action="{{ route('mitra.inden.approve', $order->id) }}" method="POST" style="margin-bottom:var(--fib-2);">
                    @csrf
                    <button type="submit" class="m-btn-primary m-btn-primary-block" style="background:linear-gradient(135deg,#16a34a,#15803d);"
                            onclick="return confirm('Setujui order inden untuk tanggal {{ $tanggal->format('d M Y') }}?')">
                        <i class="bi bi-check-circle-fill"></i> Setujui Order
                    </button>
                </form>
                <button type="button" class="m-btn-primary m-btn-primary-block"
                        style="background:#fff; color:#ef4444; border:1px solid #ef4444;"
                        onclick="document.getElementById('form-tolak').style.display='block';this.style.display='none';">
                    <i class="bi bi-x-circle"></i> Tolak Order
                </button>
                <form action="{{ route('mitra.inden.tolak', $order->id) }}" method="POST" id="form-tolak" style="display:none; margin-top:var(--fib-3);">
                    @csrf
                    <label class="m-form-label">Alasan Penolakan</label>
                    <textarea name="alasan" class="m-form-textarea" rows="3" maxlength="500"
                              placeholder="contoh: jadwal bentrok dengan order lain" required style="margin-bottom:var(--fib-2);"></textarea>
                    <button type="submit" class="m-btn-primary m-btn-primary-block" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                        <i class="bi bi-send-fill"></i> Kirim Penolakan
                    </button>
                </form>
            </div>
        </div>
    @elseif($isDpDibayar)
        <form action="{{ route('mitra.inden.mulaiKerja', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="m-btn-primary m-btn-primary-block" onclick="return confirm('Mulai kerjakan order ini?')">
                <i class="bi bi-play-fill"></i> Mulai Kerja
            </button>
        </form>
    @elseif($isDikerjakan)
        <form action="{{ route('mitra.inden.selesaiKerja', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="m-btn-primary m-btn-primary-block" style="background:linear-gradient(135deg,#16a34a,#15803d);"
                    onclick="return confirm('Tandai order ini SELESAI? Pelanggan akan diminta bayar pelunasan.')">
                <i class="bi bi-check-square"></i> Selesai Kerja
            </button>
        </form>
    @endif
</div>
@endsection
