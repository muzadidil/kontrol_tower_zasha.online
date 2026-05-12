@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Inventory</div>
            <h1 class="m-hero-title">Sparepart</h1>
        </div>
        <a href="{{ route('mitra.sparepart.create') }}" class="m-hero-action">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>

    <div style="text-align:center;">
        <div class="m-hero-stat-label">TOTAL NILAI INVENTORY</div>
        <div class="m-hero-stat-value">Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}</div>
        <div class="m-hero-stat-sub">
            {{ $stats['total_item'] }} item
            @if($stats['menipis'] > 0)
                · <span style="background:#fbbf24; color:#fff; padding:2px var(--fib-2); border-radius:var(--r-pill);">{{ $stats['menipis'] }} menipis</span>
            @endif
            @if($stats['habis'] > 0)
                · <span style="background:#ef4444; color:#fff; padding:2px var(--fib-2); border-radius:var(--r-pill);">{{ $stats['habis'] }} habis</span>
            @endif
        </div>
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif

    {{-- Search --}}
    <form method="GET" action="{{ route('mitra.sparepart.index') }}" style="margin-bottom:var(--fib-3);">
        <input type="hidden" name="status" value="{{ $filterStatus }}">
        <div style="display:flex; gap:var(--fib-1);">
            <input type="text" name="q" value="{{ $filterQuery }}" class="m-form-input" style="flex-grow:1;"
                   placeholder="Cari nama / kode / kategori...">
            <button type="submit" class="m-btn-primary" style="flex-shrink:0; padding:var(--fib-2) var(--fib-3);">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    {{-- Filter chips --}}
    <div class="m-chip-bar">
        @php
            $tabs = [
                ''         => ['Semua',     null,                   'var(--mitra-blue)'],
                'menipis'  => ['Menipis',   $stats['menipis'] ?? 0, '#f59e0b'],
                'habis'    => ['Habis',     $stats['habis'] ?? 0,   '#ef4444'],
                'nonaktif' => ['Non-aktif', null,                   'var(--ink-soft)'],
            ];
        @endphp
        @foreach($tabs as $key => [$label, $count, $color])
            @php $active = $filterStatus === $key || (!$filterStatus && $key === ''); @endphp
            <a href="{{ route('mitra.sparepart.index', ['status' => $key, 'q' => $filterQuery]) }}"
               class="m-chip {{ $active ? 'active' : '' }}"
               style="{{ $active ? 'background:'.$color.'; border-color:'.$color.';' : 'color:'.$color.'; border-color:'.$color.';' }}">
                {{ $label }}
                @if($count !== null && $count > 0)
                    <span class="m-chip-count" style="{{ $active ? 'background:rgba(255,255,255,0.25); color:#fff;' : 'background:'.$color.'; color:#fff;' }}">{{ $count }}</span>
                @endif
            </a>
        @endforeach
    </div>

    @if($spareparts->isEmpty())
        <div class="m-empty">
            <i class="bi bi-box-seam m-empty-icon"></i>
            <h3 class="m-empty-title">Inventory Kosong</h3>
            <p class="m-empty-text">
                @if($filterQuery || $filterStatus)Tidak ada hasil untuk filter ini.
                @else Tambahkan sparepart untuk lacak stok.
                @endif
            </p>
        </div>
    @else
        @foreach($spareparts as $s)
            @php
                $statusStok = $s->statusStok();
                $stokColor = match($statusStok) {
                    'habis'   => '#ef4444',
                    'menipis' => '#f59e0b',
                    'aman'    => '#16a34a',
                    default   => 'var(--ink-soft)',
                };
            @endphp
            <div class="m-card" style="{{ !$s->is_aktif ? 'opacity:0.65;' : '' }}">
                <div class="m-card-body" style="display:flex; gap:var(--fib-3);">
                    @if($s->foto_path)
                        <a href="{{ asset('storage/' . $s->foto_path) }}" target="_blank" style="flex-shrink:0;">
                            <img src="{{ asset('storage/' . $s->foto_path) }}"
                                 style="width:var(--fib-6); height:var(--fib-6); object-fit:cover; border-radius:var(--r-md);">
                        </a>
                    @else
                        <div style="width:var(--fib-6); height:var(--fib-6); border-radius:var(--r-md); background:var(--mitra-blue-soft); color:var(--mitra-blue); display:flex; align-items:center; justify-content:center; font-size:var(--t-lg); flex-shrink:0;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    @endif

                    <div style="flex-grow:1; min-width:0;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:var(--fib-1); flex-wrap:wrap; margin-bottom:var(--fib-1);">
                            <div style="flex-grow:1; min-width:0;">
                                <div style="font-weight:700; color:var(--ink); font-size:var(--t-xs); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $s->nama }}
                                    @if(!$s->is_aktif)
                                        <span style="background:var(--line); color:var(--ink-soft); padding:1px var(--fib-1); border-radius:var(--r-sm); font-size:var(--t-xxs);">non-aktif</span>
                                    @endif
                                </div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">
                                    @if($s->kode){{ $s->kode }} · @endif
                                    @if($s->kategori){{ $s->kategori }}@endif
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700; color:var(--mitra-blue); font-size:var(--t-xs);">Rp {{ number_format($s->harga, 0, ',', '.') }}</div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">per {{ $s->satuan }}</div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:var(--fib-1);">
                            <span style="background:{{ $stokColor }}20; color:{{ $stokColor }}; padding:2px var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                                <i class="bi bi-box" style="font-size:var(--t-xxs);"></i>
                                Stok: {{ $s->stok }} {{ $s->satuan }}
                                @if($statusStok === 'menipis')(min {{ $s->stok_min }})@endif
                            </span>
                            <div style="display:flex; gap:var(--fib-1);">
                                <form action="{{ route('mitra.sparepart.ubahStok', $s->id) }}" method="POST" style="display:flex; gap:var(--fib-1);">
                                    @csrf
                                    <input type="hidden" name="jumlah" value="1">
                                    <button type="submit" name="aksi" value="kurang" {{ $s->stok <= 0 ? 'disabled' : '' }}
                                            style="width:var(--fib-4); height:var(--fib-4); border-radius:50%; background:#fff; color:#ef4444; border:1px solid #ef4444; cursor:pointer; font-size:var(--t-xxs); display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <button type="submit" name="aksi" value="tambah"
                                            style="width:var(--fib-4); height:var(--fib-4); border-radius:50%; background:#fff; color:#16a34a; border:1px solid #16a34a; cursor:pointer; font-size:var(--t-xxs); display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </form>
                                <a href="{{ route('mitra.sparepart.edit', $s->id) }}"
                                   style="width:var(--fib-4); height:var(--fib-4); border-radius:50%; background:#fff; color:var(--mitra-blue); border:1px solid var(--mitra-blue); text-decoration:none; font-size:var(--t-xxs); display:flex; align-items:center; justify-content:center;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
