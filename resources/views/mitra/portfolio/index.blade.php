@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

@php $totalFeatured = $portfolios->where('is_featured', true)->count(); @endphp

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Portfolio</div>
            <h1 class="m-hero-title">Karya Saya</h1>
        </div>
        <a href="{{ route('mitra.portfolio.create') }}" class="m-hero-action">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-3);">{{ $portfolios->count() }} karya · {{ $totalFeatured }} featured</div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif

    @if($portfolios->isEmpty())
        <div class="m-empty">
            <i class="bi bi-images m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Portfolio</h3>
            <p class="m-empty-text">Tambahkan contoh karya agar pelanggan yakin sebelum order.</p>
            <a href="{{ route('mitra.portfolio.create') }}" class="m-btn-primary" style="margin-top:var(--fib-3);">
                <i class="bi bi-plus-circle"></i> Tambah Karya Pertama
            </a>
        </div>
    @else
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:var(--fib-2);">
            @foreach($portfolios as $p)
                <div class="m-card" style="position:relative; overflow:hidden; margin-bottom:0;">
                    @if($p->is_featured)
                        <div style="position:absolute; top:var(--fib-2); right:var(--fib-2); z-index:2;
                                    background:#fbbf24; color:#fff; padding:2px var(--fib-2);
                                    border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                            <i class="bi bi-star-fill"></i> Featured
                        </div>
                    @endif

                    @if($p->isImage())
                        <div style="height:var(--fib-7); background:url('{{ asset('storage/' . $p->file_path) }}') center/cover;"></div>
                    @elseif($p->file_path)
                        <div style="height:var(--fib-7); background:linear-gradient(135deg,var(--mitra-blue-tint),var(--mitra-blue-soft));
                                    display:flex; align-items:center; justify-content:center;">
                            <i class="bi bi-file-earmark-pdf" style="font-size:var(--t-xl); color:var(--mitra-blue);"></i>
                        </div>
                    @elseif($p->link_url)
                        <div style="height:var(--fib-7); background:linear-gradient(135deg,#f5f3ff,#ede9fe);
                                    display:flex; align-items:center; justify-content:center;">
                            <i class="bi bi-link-45deg" style="font-size:var(--t-xl); color:#7c3aed;"></i>
                        </div>
                    @endif

                    <div style="padding:var(--fib-2);">
                        <div style="font-weight:700; color:var(--ink); font-size:var(--t-xs); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $p->judul }}</div>
                        @if($p->kategori)
                            <span style="background:var(--mitra-blue-tint); color:var(--mitra-blue); padding:1px var(--fib-1); border-radius:var(--r-sm); font-size:var(--t-xxs);">{{ $p->kategori }}</span>
                        @endif

                        <div style="display:flex; gap:var(--fib-1); margin-top:var(--fib-2);">
                            <a href="{{ route('mitra.portfolio.edit', $p->id) }}" class="m-chip"
                               style="flex-grow:1; justify-content:center; padding:var(--fib-1); font-size:var(--t-xxs); border-color:var(--mitra-blue); color:var(--mitra-blue);">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('mitra.portfolio.toggleFeatured', $p->id) }}" method="POST" style="flex-grow:1;">
                                @csrf
                                <button type="submit" class="m-chip"
                                        style="width:100%; justify-content:center; padding:var(--fib-1); font-size:var(--t-xxs); border-color:#fbbf24; color:{{ $p->is_featured ? '#fff' : '#fbbf24' }}; {{ $p->is_featured ? 'background:#fbbf24;' : '' }} cursor:pointer;">
                                    <i class="bi {{ $p->is_featured ? 'bi-star-fill' : 'bi-star' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('mitra.portfolio.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus &quot;{{ $p->judul }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="m-chip" style="padding:var(--fib-1); font-size:var(--t-xxs); border-color:#ef4444; color:#ef4444; cursor:pointer;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="m-alert m-alert-info" style="margin-top:var(--fib-3);">
            <i class="bi bi-info-circle-fill"></i>
            <div>Karya bertanda <i class="bi bi-star-fill" style="color:#fbbf24;"></i> <strong>Featured</strong> akan ditampilkan terlebih dulu di halaman detail mitra untuk pelanggan.</div>
        </div>
    @endif
</div>
@endsection
