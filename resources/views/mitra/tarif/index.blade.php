@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Keuangan</div>
            <h1 class="m-hero-title">Daftar Tarif Layanan</h1>
        </div>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-2);">
        Komisi Zasha: {{ number_format($komisiPersen, 1) }}% otomatis ditambahkan
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif

    <a href="{{ route('mitra.tarif.create') }}" class="m-btn-primary m-btn-primary-block" style="margin-bottom:var(--fib-3);">
        <i class="bi bi-plus-circle"></i> Tambah Tarif Baru
    </a>

    @if($tarifs->isEmpty())
        <div class="m-empty">
            <i class="bi bi-cash-stack m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Tarif</h3>
            <p class="m-empty-text">Tambahkan tarif layanan Anda agar pelanggan tahu harga sebelum order.</p>
        </div>
    @else
        @foreach($tarifs as $t)
            <div class="m-card" style="{{ !$t->is_aktif ? 'opacity:0.65;' : '' }}">
                <div class="m-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:var(--fib-2); margin-bottom:var(--fib-2);">
                        <div style="flex-grow:1; min-width:0;">
                            <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">{{ $t->keterangan }}</div>
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft);">per {{ $t->satuan }}</div>
                        </div>
                        @if($t->is_aktif)
                            <span style="background:#d1fae5; color:#065f46; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">Aktif</span>
                        @else
                            <span style="background:var(--line); color:var(--ink-soft); padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">Non-aktif</span>
                        @endif
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; padding:var(--fib-2) var(--fib-3);
                                background:var(--mitra-blue-tint); border-radius:var(--r-md); font-size:var(--t-xs);">
                        <div>
                            <div style="color:var(--ink-soft); font-size:var(--t-xxs);">Anda terima</div>
                            <div style="font-weight:700; color:#16a34a;">Rp {{ number_format($t->nominal, 0, ',', '.') }}</div>
                        </div>
                        <i class="bi bi-arrow-right" style="color:var(--ink-soft);"></i>
                        <div style="text-align:right;">
                            <div style="color:var(--ink-soft); font-size:var(--t-xxs);">Pelanggan bayar</div>
                            <div style="font-weight:700; color:var(--mitra-blue);">Rp {{ number_format($t->hargaPelanggan(), 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <div style="display:flex; gap:var(--fib-1); margin-top:var(--fib-3);">
                        <a href="{{ route('mitra.tarif.edit', $t->id) }}"
                           class="m-chip" style="flex-grow:1; justify-content:center; border-color:var(--mitra-blue); color:var(--mitra-blue);">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('mitra.tarif.toggle', $t->id) }}" method="POST" style="flex-grow:1;">
                            @csrf
                            <button type="submit" class="m-chip" style="width:100%; justify-content:center; border:1px solid {{ $t->is_aktif ? 'var(--ink-soft)' : '#16a34a' }}; color:{{ $t->is_aktif ? 'var(--ink-soft)' : '#16a34a' }}; cursor:pointer;">
                                <i class="bi {{ $t->is_aktif ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                {{ $t->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                        <form action="{{ route('mitra.tarif.destroy', $t->id) }}" method="POST"
                              onsubmit="return confirm('Hapus tarif &quot;{{ $t->keterangan }}&quot;?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="m-chip" style="border-color:#ef4444; color:#ef4444; cursor:pointer;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="m-alert m-alert-info" style="margin-top:var(--fib-3);">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                <strong>Cara kerja komisi:</strong> Pelanggan bayar tarif Anda + {{ number_format($komisiPersen, 1) }}% komisi Zasha.
                Anda menerima 100% tarif yang Anda set.
            </div>
        </div>
    @endif
</div>
@endsection
