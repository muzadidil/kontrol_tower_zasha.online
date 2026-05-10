@extends('layouts.mitra')

@section('content')
<div class="page-pad stack-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center" style="padding-top: var(--fib-2);">
        <div>
            <div class="label-up">Order Masuk</div>
            <h5 class="fw-bold mb-0 t-lg" style="margin-top: var(--fib-1);">Daftar Pesanan</h5>
        </div>
        <div style="width: var(--fib-5); height: var(--fib-5); background: var(--mitra-gold-soft); border-radius: var(--r-md); display: inline-flex; align-items: center; justify-content: center;">
            <span class="fw-bold" style="color: var(--mitra-gold); font-size: var(--t-sm);">{{ $pesanan->count() }}</span>
        </div>
    </div>

    {{-- Filter Tabs (golden ratio: 4 tabs in 1 row) --}}
    <div class="d-flex" style="gap: var(--fib-2); overflow-x: auto; padding-bottom: var(--fib-2);">
        @foreach([
            'semua' => ['Semua', 'bi-collection'],
            'aktif' => ['Aktif', 'bi-clock'],
            'selesai' => ['Selesai', 'bi-check-circle'],
            'dibatalkan' => ['Batal', 'bi-x-circle'],
        ] as $key => $tab)
            <button type="button" class="btn"
                style="background: {{ $loop->first ? 'var(--mitra-green)' : 'var(--surface)' }};
                       color: {{ $loop->first ? '#fff' : 'var(--ink-soft)' }};
                       border: 1px solid {{ $loop->first ? 'var(--mitra-green)' : 'var(--line)' }};
                       border-radius: var(--r-pill);
                       font-size: var(--t-xs); font-weight: 700;
                       padding: var(--fib-2) var(--fib-3); white-space: nowrap;">
                <i class="bi {{ $tab[1] }} me-1"></i>{{ $tab[0] }}
            </button>
        @endforeach
    </div>

    {{-- List --}}
    @if($pesanan->isEmpty())
        <div class="card-custom text-center" style="padding: var(--fib-6) var(--fib-4);">
            <div style="width: var(--fib-7); height: var(--fib-7); background: var(--bg-app); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto var(--fib-3);">
                <i class="bi bi-clipboard-x" style="font-size: var(--fib-5); color: var(--ink-soft);"></i>
            </div>
            <h6 class="fw-bold t-sm mb-1">Belum Ada Pesanan</h6>
            <div class="t-xs" style="color: var(--ink-soft);">Pesanan baru akan muncul di sini.</div>
        </div>
    @else
        <div class="stack-3">
            @foreach($pesanan as $p)
                @php
                    $statusKey = strtolower($p->status_pesanan ?? '');
                    $bg = '#fef3c7'; $color = '#92400e';
                    if(in_array($statusKey, ['selesai'])) { $bg = '#d1fae5'; $color = '#065f46'; }
                    elseif(in_array($statusKey, ['dibatalkan','batal'])) { $bg = '#fee2e2'; $color = '#991b1b'; }
                @endphp
                <div class="card-custom" style="padding: var(--fib-3);">
                    {{-- Header row --}}
                    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: var(--fib-2);">
                        <span class="fw-bold allow-select t-sm" style="font-family: 'SF Mono', monospace;">
                            #{{ $p->id_pesanan }}
                        </span>
                        <span style="background: {{ $bg }}; color: {{ $color }}; font-size: var(--t-xxs); padding: var(--fib-1) var(--fib-3); border-radius: var(--r-pill); font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase;">
                            {{ ucfirst($p->status_pesanan) }}
                        </span>
                    </div>

                    {{-- Date --}}
                    <div class="d-flex align-items-center" style="gap: var(--fib-1); color: var(--ink-soft); font-size: var(--t-xs); margin-bottom: var(--fib-2);">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y · H:i') }}</span>
                    </div>

                    {{-- Footer (golden ratio: total | action) --}}
                    <div class="d-flex justify-content-between align-items-center" style="padding-top: var(--fib-2); border-top: 1px solid var(--line);">
                        <div>
                            <div class="t-xxs label-up">Pendapatan</div>
                            <div class="fw-bold t-sm" style="color: var(--mitra-green);">
                                Rp {{ number_format($p->biaya_jasa ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                        <a href="#" class="btn-mitra-primary" style="padding: var(--fib-1) var(--fib-3); font-size: var(--t-xxs);">
                            Detail <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
