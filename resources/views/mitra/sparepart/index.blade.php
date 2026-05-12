@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">INVENTORY</div>
            <h5 class="fw-bold m-0">Sparepart</h5>
        </div>
        <a href="{{ route('mitra.sparepart.create') }}"
           class="ms-auto text-white text-decoration-none small fw-bold"
           style="background:rgba(255,255,255,.18);padding:6px 14px;border-radius:999px;">
            <i class="bi bi-plus-lg me-1"></i> Tambah
        </a>
    </div>

    {{-- Total nilai inventory --}}
    <div class="text-center">
        <div style="font-size:.7rem;opacity:.8;">TOTAL NILAI INVENTORY</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:-.02em;">
            Rp {{ number_format($stats['total_nilai'], 0, ',', '.') }}
        </div>
        <div style="font-size:.7rem;opacity:.75;">
            {{ $stats['total_item'] }} item
            @if($stats['menipis'] > 0)
                · <span style="background:#fbbf24;color:#fff;padding:2px 8px;border-radius:999px;">
                    {{ $stats['menipis'] }} menipis
                </span>
            @endif
            @if($stats['habis'] > 0)
                · <span style="background:#ef4444;color:#fff;padding:2px 8px;border-radius:999px;">
                    {{ $stats['habis'] }} habis
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
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm small">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Filter Search --}}
    <form method="GET" action="{{ route('mitra.sparepart.index') }}" class="mb-3">
        <input type="hidden" name="status" value="{{ $filterStatus }}">
        <div class="input-group input-group-sm">
            <input type="text" name="q" value="{{ $filterQuery }}"
                   class="form-control rounded-start-3"
                   placeholder="Cari nama / kode / kategori...">
            <button type="submit" class="btn btn-primary rounded-end-3 px-3">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    {{-- Filter Tabs --}}
    <div class="d-flex gap-2 mb-3 overflow-auto pb-2" style="scrollbar-width:thin;">
        @php
            $tabs = [
                ''         => ['Semua',     null,                   '#005aa9'],
                'menipis'  => ['Menipis',   $stats['menipis'] ?? 0, '#f59e0b'],
                'habis'    => ['Habis',     $stats['habis'] ?? 0,   '#ef4444'],
                'nonaktif' => ['Non-aktif', null,                   '#64748b'],
            ];
        @endphp
        @foreach($tabs as $key => [$label, $count, $color])
            @php $active = $filterStatus === $key || (!$filterStatus && $key === ''); @endphp
            <a href="{{ route('mitra.sparepart.index', ['status' => $key, 'q' => $filterQuery]) }}"
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

    {{-- List Spareparts --}}
    @if($spareparts->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-box-seam" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Inventory Kosong</h6>
            <p class="text-muted small">
                @if($filterQuery || $filterStatus)
                    Tidak ada hasil untuk filter ini.
                @else
                    Tambahkan sparepart untuk lacak stok.
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
                    default   => '#64748b',
                };
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-2 {{ !$s->is_aktif ? 'opacity-75' : '' }}">
                <div class="card-body p-3">
                    <div class="d-flex gap-3">
                        {{-- Foto/Icon --}}
                        @if($s->foto_path)
                            <a href="{{ asset('storage/' . $s->foto_path) }}" target="_blank" class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $s->foto_path) }}"
                                     style="width:60px;height:60px;object-fit:cover;border-radius:10px;">
                            </a>
                        @else
                            <div style="width:60px;height:60px;border-radius:10px;background:#eff6ff;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="bi bi-box-seam" style="color:#005aa9;font-size:1.5rem;"></i>
                            </div>
                        @endif

                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start gap-1 flex-wrap mb-1">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold small text-truncate" style="color:#1e293b;">
                                        {{ $s->nama }}
                                        @if(!$s->is_aktif)
                                            <span class="badge bg-secondary ms-1" style="font-size:.55rem;">non-aktif</span>
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size:.65rem;">
                                        @if($s->kode){{ $s->kode }} · @endif
                                        @if($s->kategori){{ $s->kategori }}@endif
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold small" style="color:#005aa9;">
                                        Rp {{ number_format($s->harga, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size:.6rem;">per {{ $s->satuan }}</small>
                                </div>
                            </div>

                            {{-- Stok bar --}}
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="badge rounded-pill px-2"
                                      style="background:{{ $stokColor }}20;color:{{ $stokColor }};font-size:.65rem;">
                                    <i class="bi bi-box" style="font-size:.6rem;"></i>
                                    Stok: {{ $s->stok }} {{ $s->satuan }}
                                    @if($statusStok === 'menipis')
                                        (min {{ $s->stok_min }})
                                    @endif
                                </span>
                                <div class="d-flex gap-1">
                                    {{-- Quick stock buttons --}}
                                    <form action="{{ route('mitra.sparepart.ubahStok', $s->id) }}" method="POST" class="d-flex gap-1">
                                        @csrf
                                        <input type="hidden" name="jumlah" value="1">
                                        <button type="submit" name="aksi" value="kurang"
                                                class="btn btn-sm btn-outline-danger rounded-circle p-0"
                                                style="width:24px;height:24px;font-size:.7rem;"
                                                {{ $s->stok <= 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        <button type="submit" name="aksi" value="tambah"
                                                class="btn btn-sm btn-outline-success rounded-circle p-0"
                                                style="width:24px;height:24px;font-size:.7rem;">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('mitra.sparepart.edit', $s->id) }}"
                                       class="btn btn-sm btn-outline-primary rounded-circle p-0"
                                       style="width:24px;height:24px;font-size:.7rem;">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
