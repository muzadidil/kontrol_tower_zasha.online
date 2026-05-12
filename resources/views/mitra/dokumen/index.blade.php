@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

@php
    $totalFinal = $dokumens->where('is_final', true)->count();
    $totalSize = $dokumens->sum('size_bytes');
    $sizeFormatted = $totalSize < 1024*1024
        ? round($totalSize / 1024, 1) . ' KB'
        : round($totalSize / (1024*1024), 1) . ' MB';
@endphp

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="javascript:history.back()" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Dokumen Kerja</div>
            <h1 class="m-hero-title">Order #{{ $tracking->id }}</h1>
        </div>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-3);">
        {{ ucfirst($tracking->order_type) }} · {{ $dokumens->count() }} dokumen
        @if($totalFinal > 0)
            · <span style="background:#fbbf24; color:#fff; padding:2px var(--fib-2); border-radius:var(--r-pill);">{{ $totalFinal }} final</span>
        @endif
        · {{ $sizeFormatted }}
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="m-alert m-alert-error" style="flex-direction:column; align-items:flex-start;">
            @foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill"></i>{{ $err }}</div>@endforeach
        </div>
    @endif

    {{-- Form Upload --}}
    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">
                <i class="bi bi-cloud-upload m-section-title-icon"></i> Upload Dokumen Baru
            </h2>
            <form action="{{ route('mitra.dokumen.upload', $tracking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">Judul <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="judul" class="m-form-input"
                           placeholder="contoh: Draft Desain Logo v1" value="{{ old('judul') }}" maxlength="200" required>
                </div>
                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">File <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="file" class="m-form-input" required>
                    <div class="m-form-help">Max 25 MB · semua tipe file diterima</div>
                </div>
                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">Catatan</label>
                    <textarea name="catatan" class="m-form-textarea" rows="2" maxlength="500"
                              placeholder="contoh: revisi pertama berdasar feedback"></textarea>
                </div>
                <label style="display:flex; align-items:center; gap:var(--fib-2); cursor:pointer; margin-bottom:var(--fib-3); font-size:var(--t-xs); color:var(--ink);">
                    <input type="checkbox" name="is_final" value="1" style="accent-color:#fbbf24;">
                    <span style="font-weight:700;"><i class="bi bi-bookmark-star-fill" style="color:#fbbf24;"></i> Tandai sebagai versi FINAL</span>
                </label>
                <button type="submit" class="m-btn-primary m-btn-primary-block">
                    <i class="bi bi-cloud-upload"></i> Upload Dokumen
                </button>
            </form>
        </div>
    </div>

    {{-- List Dokumen --}}
    @if($dokumens->isEmpty())
        <div class="m-empty">
            <i class="bi bi-folder2-open m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Dokumen</h3>
            <p class="m-empty-text">Upload hasil kerja agar pelanggan bisa download dan review.</p>
        </div>
    @else
        @foreach($dokumens as $d)
            <div class="m-card" style="{{ $d->is_final ? 'border-left:4px solid #fbbf24;' : '' }}">
                <div class="m-card-body" style="display:flex; gap:var(--fib-3); align-items:flex-start;">
                    <div style="width:var(--fib-6); height:var(--fib-6); border-radius:var(--r-md); background:var(--mitra-blue-soft); color:var(--mitra-blue); display:flex; align-items:center; justify-content:center; font-size:var(--t-lg); flex-shrink:0;">
                        <i class="bi {{ $d->icon_class }}"></i>
                    </div>
                    <div style="flex-grow:1; min-width:0;">
                        <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $d->judul }}
                            @if($d->is_final)
                                <span style="background:#fef3c7; color:#92400e; padding:1px var(--fib-1); border-radius:var(--r-sm); font-size:var(--t-xxs);">
                                    <i class="bi bi-bookmark-star-fill"></i> Final
                                </span>
                            @endif
                        </div>
                        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $d->size_formatted }} · {{ $d->created_at->diffForHumans() }}</div>
                        @if($d->catatan)
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft); margin-top:var(--fib-1);">{{ $d->catatan }}</div>
                        @endif
                        <div style="display:flex; gap:var(--fib-1); margin-top:var(--fib-2);">
                            <a href="{{ asset('storage/' . $d->file_path) }}" target="_blank"
                               class="m-btn-primary" style="flex-grow:1; padding:var(--fib-1) var(--fib-2); font-size:var(--t-xxs);">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <form action="{{ route('mitra.dokumen.toggleFinal', $d->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="m-chip" style="border-color:#fbbf24; color:{{ $d->is_final ? '#fff' : '#fbbf24' }}; {{ $d->is_final ? 'background:#fbbf24;' : '' }} cursor:pointer; padding:var(--fib-1) var(--fib-2);">
                                    <i class="bi {{ $d->is_final ? 'bi-bookmark-star-fill' : 'bi-bookmark-star' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('mitra.dokumen.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus &quot;{{ $d->judul }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="m-chip" style="border-color:#ef4444; color:#ef4444; cursor:pointer; padding:var(--fib-1) var(--fib-2);">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="m-alert m-alert-info">
        <i class="bi bi-info-circle-fill"></i>
        <div><strong>Tips:</strong> Tandai dokumen <i class="bi bi-bookmark-star-fill" style="color:#fbbf24;"></i> <strong>Final</strong> agar pelanggan tahu mana versi yang sudah disepakati.</div>
    </div>
</div>
@endsection
