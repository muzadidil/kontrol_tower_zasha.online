@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">REPUTASI</div>
            <h5 class="fw-bold m-0">Rating & Ulasan</h5>
        </div>
    </div>

    {{-- Rating rata-rata besar di tengah --}}
    <div class="text-center">
        <div style="font-size:.7rem;opacity:.8;">RATING RATA-RATA</div>
        <div class="d-flex align-items-center justify-content-center gap-2 my-1">
            <div style="font-size:2.5rem;font-weight:800;letter-spacing:-.02em;">
                {{ number_format($stats['rata_rata'], 1) }}
            </div>
            <div style="font-size:1.2rem;color:#ffd700;">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($stats['rata_rata']) ? 'bi-star-fill' : 'bi-star' }}"></i>
                @endfor
            </div>
        </div>
        <div style="font-size:.7rem;opacity:.7;">
            dari {{ $stats['total'] }} ulasan
            @if($stats['belum_dibalas'] > 0)
                · <span style="background:#ef4444;color:#fff;padding:2px 8px;border-radius:999px;font-weight:700;">
                    {{ $stats['belum_dibalas'] }} belum dibalas
                </span>
            @endif
        </div>
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Distribusi Bintang --}}
    @if($stats['total'] > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b;font-size:.85rem;">Distribusi Bintang</h6>
            @for($b = 5; $b >= 1; $b--)
                @php
                    $count = $stats['distribusi'][$b] ?? 0;
                    $pct = $stats['total'] > 0 ? round(($count / $stats['total']) * 100) : 0;
                @endphp
                <a href="{{ route('mitra.rating.index', array_merge(request()->query(), ['bintang' => $b])) }}"
                   class="d-flex align-items-center gap-2 text-decoration-none mb-1"
                   style="color:#1e293b;">
                    <div style="min-width:42px;">
                        <span class="fw-bold small">{{ $b }}</span>
                        <i class="bi bi-star-fill" style="color:#ffd700;font-size:.7rem;"></i>
                    </div>
                    <div class="flex-grow-1" style="height:8px;background:#f1f5f9;border-radius:999px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;
                                    background:linear-gradient(90deg,#fbbf24,#f59e0b);"></div>
                    </div>
                    <div style="min-width:30px;text-align:right;font-size:.75rem;color:#64748b;">
                        {{ $count }}
                    </div>
                </a>
            @endfor
        </div>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="d-flex gap-2 mb-3 overflow-auto pb-2" style="scrollbar-width:thin;">
        @php
            $filterOpts = [
                'all'           => ['Semua',         null,   '#005aa9'],
                'belum-dibalas' => ['Belum Dibalas', 'ef4444', '#ef4444'],
                'sudah-dibalas' => ['Sudah Dibalas', null,   '#16a34a'],
            ];
        @endphp
        @foreach($filterOpts as $key => [$label, $bg, $color])
            @php $active = $filter === $key; @endphp
            <a href="{{ route('mitra.rating.index', ['filter' => $key, 'bintang' => $bintang]) }}"
               class="text-decoration-none flex-shrink-0"
               style="padding:6px 14px;border-radius:999px;font-size:.75rem;font-weight:600;
                      white-space:nowrap;
                      background:{{ $active ? $color : '#fff' }};
                      color:{{ $active ? '#fff' : $color }};
                      border:1px solid {{ $color }};">
                {{ $label }}
            </a>
        @endforeach
        @if($bintang)
            <a href="{{ route('mitra.rating.index', ['filter' => $filter]) }}"
               class="text-decoration-none flex-shrink-0"
               style="padding:6px 14px;border-radius:999px;font-size:.75rem;font-weight:600;
                      background:#f1f5f9;color:#64748b;">
                <i class="bi bi-x-lg me-1"></i>{{ $bintang }} <i class="bi bi-star-fill" style="color:#ffd700;font-size:.65rem;"></i>
            </a>
        @endif
    </div>

    {{-- Daftar Ulasan --}}
    @if($ratings->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-chat-square-text" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Ulasan</h6>
            <p class="text-muted small mb-0">
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
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-1">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:50%;
                                            background:#eff6ff;color:#005aa9;
                                            display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <div class="fw-bold small" style="color:#1e293b;">{{ $nama }}</div>
                                    <small class="text-muted" style="font-size:.65rem;">
                                        {{ $orderTypeLabel }} · {{ $r->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div style="color:#ffd700;font-size:.85rem;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $r->bintang ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </div>
                    </div>

                    {{-- Isi ulasan --}}
                    @if($r->ulasan)
                        <div class="mb-3" style="color:#1e293b;font-size:.85rem;line-height:1.5;">
                            {{ $r->ulasan }}
                        </div>
                    @else
                        <div class="mb-3 text-muted fst-italic small">
                            (Tidak ada teks ulasan)
                        </div>
                    @endif

                    @if($r->foto_url)
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $r->foto_url) }}" target="_blank">
                                <img src="{{ asset('storage/' . $r->foto_url) }}"
                                     style="max-height:140px;border-radius:8px;">
                            </a>
                        </div>
                    @endif

                    {{-- Balasan mitra --}}
                    @if($r->balasan_mitra)
                        <div class="rounded-3 p-3" style="background:#eff6ff;border-left:3px solid #005aa9;">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <small class="fw-bold" style="color:#005aa9;">
                                    <i class="bi bi-reply-fill me-1"></i>Balasan Anda
                                </small>
                                <small class="text-muted" style="font-size:.65rem;">
                                    {{ $r->balasan_at?->diffForHumans() }}
                                </small>
                            </div>
                            <div style="color:#1e293b;font-size:.8rem;line-height:1.5;">
                                {{ $r->balasan_mitra }}
                            </div>
                            @if($mitra->hasFeature('ulasan'))
                                <form action="{{ route('mitra.rating.hapusBalasan', $r->id) }}" method="POST"
                                      class="mt-2"
                                      onsubmit="return confirm('Hapus balasan ini? Anda bisa balas ulang nanti.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0"
                                            style="font-size:.7rem;text-decoration:none;">
                                        <i class="bi bi-trash"></i> Hapus balasan
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif($mitra->hasFeature('ulasan'))
                        {{-- Form balas (hanya kalau punya fitur 'ulasan') --}}
                        <form action="{{ route('mitra.rating.balas', $r->id) }}" method="POST" class="mt-2">
                            @csrf
                            <input type="hidden" name="filter" value="{{ $filter }}">
                            @if($bintang)
                                <input type="hidden" name="bintang" value="{{ $bintang }}">
                            @endif
                            <div class="input-group input-group-sm">
                                <input type="text" name="balasan"
                                       class="form-control rounded-start-3"
                                       placeholder="Tulis balasan..."
                                       maxlength="500" required>
                                <button type="submit" class="btn btn-primary rounded-end-3 px-3">
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
