@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Reputasi</div>
            <h1 class="m-hero-title">Rating & Ulasan</h1>
        </div>
    </div>

    <div style="text-align:center;">
        <div class="m-hero-stat-label">RATING RATA-RATA</div>
        <div style="display:flex; align-items:center; justify-content:center; gap:var(--fib-2); margin-top:var(--fib-1);">
            <div style="font-size:var(--t-2xl); font-weight:800; letter-spacing:-0.02em;">{{ number_format($stats['rata_rata'], 1) }}</div>
            <div style="font-size:var(--t-md); color:#fbbf24;">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($stats['rata_rata']) ? 'bi-star-fill' : 'bi-star' }}"></i>
                @endfor
            </div>
        </div>
        <div class="m-hero-stat-sub">
            dari {{ $stats['total'] }} ulasan
            @if($stats['belum_dibalas'] > 0)
                · <span style="background:#ef4444; color:#fff; padding:2px var(--fib-2); border-radius:var(--r-pill); font-weight:700;">{{ $stats['belum_dibalas'] }} belum dibalas</span>
            @endif
        </div>
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif

    {{-- Distribusi Bintang --}}
    @if($stats['total'] > 0)
    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">Distribusi Bintang</h2>
            @for($b = 5; $b >= 1; $b--)
                @php
                    $count = $stats['distribusi'][$b] ?? 0;
                    $pct = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
                @endphp
                <a href="{{ route('mitra.rating.index', array_merge(request()->query(), ['bintang' => $b])) }}"
                   style="display:flex; align-items:center; gap:var(--fib-2); text-decoration:none; color:var(--ink); margin-bottom:var(--fib-1);">
                    <div style="min-width:var(--fib-6); display:flex; align-items:center; gap:var(--fib-1);">
                        <span style="font-weight:700; font-size:var(--t-xs);">{{ $b }}</span>
                        <i class="bi bi-star-fill" style="color:#fbbf24; font-size:var(--t-xxs);"></i>
                    </div>
                    <div style="flex-grow:1; height:var(--fib-2); background:var(--line); border-radius:var(--r-pill); overflow:hidden;">
                        <div style="height:100%; width:{{ $pct }}%; background:linear-gradient(90deg,#fbbf24,#f59e0b);"></div>
                    </div>
                    <div style="min-width:var(--fib-5); text-align:right; font-size:var(--t-xs); color:var(--ink-soft);">{{ $count }}</div>
                </a>
            @endfor
        </div>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="m-chip-bar">
        @php
            $filterOpts = [
                'all'           => ['Semua',         'var(--mitra-blue)'],
                'belum-dibalas' => ['Belum Dibalas', '#ef4444'],
                'sudah-dibalas' => ['Sudah Dibalas', '#16a34a'],
            ];
        @endphp
        @foreach($filterOpts as $key => [$label, $color])
            @php $active = $filter === $key; @endphp
            <a href="{{ route('mitra.rating.index', ['filter' => $key, 'bintang' => $bintang]) }}"
               class="m-chip {{ $active ? 'active' : '' }}"
               style="{{ $active ? 'background:'.$color.'; border-color:'.$color.';' : 'color:'.$color.'; border-color:'.$color.';' }}">
                {{ $label }}
            </a>
        @endforeach
        @if($bintang)
            <a href="{{ route('mitra.rating.index', ['filter' => $filter]) }}" class="m-chip" style="background:var(--line); color:var(--ink-soft); border-color:var(--line);">
                <i class="bi bi-x-lg"></i> {{ $bintang }} <i class="bi bi-star-fill" style="color:#fbbf24; font-size:var(--t-xxs);"></i>
            </a>
        @endif
    </div>

    {{-- Daftar Ulasan --}}
    @if($ratings->isEmpty())
        <div class="m-empty">
            <i class="bi bi-chat-square-text m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Ulasan</h3>
            <p class="m-empty-text">
                @if($filter !== 'all' || $bintang)
                    Coba ubah filter di atas.
                @else
                    Ulasan dari pelanggan akan muncul di sini setelah order selesai.
                @endif
            </p>
        </div>
    @else
        @foreach($ratings as $r)
            @php
                $pl = $pelanggans->get($r->penilai_id);
                $nama = $r->is_anonim
                    ? 'Pelanggan Anonim'
                    : ($pl->nama_panggilan ?? $pl->nama_pelanggan ?? 'Pelanggan');
                $orderTypeLabel = ucfirst($r->order_type);
            @endphp
            <div class="m-card">
                <div class="m-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--fib-2); flex-wrap:wrap; gap:var(--fib-1);">
                        <div style="display:flex; align-items:center; gap:var(--fib-2);">
                            <div style="width:var(--fib-5); height:var(--fib-5); border-radius:50%; background:var(--mitra-blue-soft); color:var(--mitra-blue); display:flex; align-items:center; justify-content:center;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div>
                                <div style="font-weight:700; font-size:var(--t-xs); color:var(--ink);">{{ $nama }}</div>
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $orderTypeLabel }} · {{ $r->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <div style="color:#fbbf24; font-size:var(--t-sm);">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $r->bintang ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                    </div>

                    @if($r->ulasan)
                        <div style="margin-bottom:var(--fib-3); color:var(--ink); font-size:var(--t-xs); line-height:1.5;">{{ $r->ulasan }}</div>
                    @else
                        <div style="margin-bottom:var(--fib-3); color:var(--ink-soft); font-style:italic; font-size:var(--t-xs);">(Tidak ada teks ulasan)</div>
                    @endif

                    @if($r->foto_url)
                        <div style="margin-bottom:var(--fib-3);">
                            <a href="{{ asset('storage/' . $r->foto_url) }}" target="_blank">
                                <img src="{{ asset('storage/' . $r->foto_url) }}" style="max-height:var(--fib-8); border-radius:var(--r-sm);">
                            </a>
                        </div>
                    @endif

                    @if($r->balasan_mitra)
                        <div style="background:var(--mitra-blue-tint); border-left:3px solid var(--mitra-blue); border-radius:var(--r-sm); padding:var(--fib-2) var(--fib-3);">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:var(--fib-1);">
                                <span style="font-weight:700; color:var(--mitra-blue); font-size:var(--t-xxs);">
                                    <i class="bi bi-reply-fill"></i> Balasan Anda
                                </span>
                                <span style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $r->balasan_at?->diffForHumans() }}</span>
                            </div>
                            <div style="color:var(--ink); font-size:var(--t-xs); line-height:1.5;">{{ $r->balasan_mitra }}</div>
                            @if($mitra->hasFeature('ulasan'))
                                <form action="{{ route('mitra.rating.hapusBalasan', $r->id) }}" method="POST" style="margin-top:var(--fib-2);"
                                      onsubmit="return confirm('Hapus balasan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#ef4444; font-size:var(--t-xxs); cursor:pointer; padding:0;">
                                        <i class="bi bi-trash"></i> Hapus balasan
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif($mitra->hasFeature('ulasan'))
                        <form action="{{ route('mitra.rating.balas', $r->id) }}" method="POST" style="margin-top:var(--fib-2);">
                            @csrf
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            @if($bintang)<input type="hidden" name="bintang" value="{{ $bintang }}">@endif
                            <div style="display:flex; gap:var(--fib-1);">
                                <input type="text" name="balasan" class="m-form-input" style="flex-grow:1;"
                                       placeholder="Tulis balasan..." maxlength="500" required>
                                <button type="submit" class="m-btn-primary" style="flex-shrink:0; padding:var(--fib-2) var(--fib-3);">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
