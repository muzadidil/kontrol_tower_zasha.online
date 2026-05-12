@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

@php
    $totalFoto = collect($foto)->sum(fn($g) => $g->count());
@endphp

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="javascript:history.back()" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Foto Bukti</div>
            <h1 class="m-hero-title">Order #{{ $tracking->id }}</h1>
        </div>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-3);">
        {{ ucfirst($tracking->order_type) }} · {{ $totalFoto }} foto · Status: {{ $tracking->status }}
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
                <i class="bi bi-cloud-upload m-section-title-icon"></i> Upload Foto Bukti
            </h2>

            <form action="{{ route('mitra.foto-bukti.upload', $tracking->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">Tipe Foto <span style="color:#ef4444;">*</span></label>
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:var(--fib-1);">
                        @foreach(\App\Models\OrderFotoBukti::TIPE_LABEL as $key => $label)
                            <label style="cursor:pointer;">
                                <input type="radio" name="tipe" value="{{ $key }}"
                                       {{ old('tipe', 'sebelum') === $key ? 'checked' : '' }} required
                                       class="tipe-radio" data-key="{{ $key }}"
                                       style="display:none;">
                                <div class="tipe-card" data-target="{{ $key }}"
                                     style="padding:var(--fib-2); border:1px solid var(--line); border-radius:var(--r-sm); text-align:center; transition:all 0.2s;">
                                    @if($key === 'sebelum')<i class="bi bi-arrow-up-circle" style="color:var(--mitra-blue);"></i>
                                    @elseif($key === 'proses')<i class="bi bi-arrow-repeat" style="color:#f59e0b;"></i>
                                    @else<i class="bi bi-check-circle" style="color:#16a34a;"></i>
                                    @endif
                                    <div style="font-size:var(--t-xxs); color:var(--ink); font-weight:700; margin-top:var(--fib-1);">{{ $label }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-bottom:var(--fib-2);">
                    <label class="m-form-label">Foto <span style="color:#ef4444;">*</span></label>
                    <input type="file" name="foto[]" class="m-form-input"
                           accept="image/jpeg,image/png,image/webp" capture="environment"
                           multiple required>
                    <div class="m-form-help">Bisa pilih beberapa foto · max 10 file · JPG/PNG/WEBP max 8MB</div>
                </div>

                <div style="margin-bottom:var(--fib-3);">
                    <label class="m-form-label">Catatan</label>
                    <textarea name="catatan" class="m-form-textarea" rows="2" maxlength="500"
                              placeholder="contoh: AC sudah dibersihkan, tinggal pasang covernya"></textarea>
                </div>

                <button type="submit" class="m-btn-primary m-btn-primary-block">
                    <i class="bi bi-cloud-upload"></i> Upload Foto
                </button>
            </form>
        </div>
    </div>

    {{-- Galeri per Tipe --}}
    @foreach(\App\Models\OrderFotoBukti::TIPE_LABEL as $tipe => $label)
        @php $fotoTipe = $foto->get($tipe) ?? collect(); @endphp
        @if($fotoTipe->isNotEmpty())
            <div class="m-card">
                <div class="m-card-body">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--fib-3);">
                        <h3 style="font-size:var(--t-sm); font-weight:700; color:var(--ink); margin:0;">
                            @if($tipe === 'sebelum')<i class="bi bi-arrow-up-circle" style="color:var(--mitra-blue);"></i>
                            @elseif($tipe === 'proses')<i class="bi bi-arrow-repeat" style="color:#f59e0b;"></i>
                            @else<i class="bi bi-check-circle" style="color:#16a34a;"></i>
                            @endif
                            {{ $label }}
                        </h3>
                        <span style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $fotoTipe->count() }} foto</span>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:var(--fib-1);">
                        @foreach($fotoTipe as $f)
                            <div style="position:relative;">
                                <a href="{{ asset('storage/' . $f->file_path) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $f->file_path) }}"
                                         style="width:100%; height:var(--fib-7); object-fit:cover; border-radius:var(--r-sm);">
                                </a>
                                <form action="{{ route('mitra.foto-bukti.destroy', $f->id) }}" method="POST"
                                      style="position:absolute; top:var(--fib-1); right:var(--fib-1);"
                                      onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            style="width:var(--fib-4); height:var(--fib-4); border-radius:50%;
                                                   background:rgba(239,68,68,0.85); color:#fff; border:none; cursor:pointer;
                                                   font-size:var(--t-xxs); display:flex; align-items:center; justify-content:center;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                                @if($f->catatan)
                                    <div style="font-size:var(--t-xxs); color:var(--ink-soft); margin-top:var(--fib-1); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                         title="{{ $f->catatan }}">{{ $f->catatan }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if($totalFoto === 0)
        <div class="m-empty">
            <i class="bi bi-camera m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Foto Bukti</h3>
            <p class="m-empty-text">Upload foto sebelum/proses/sesudah pekerjaan untuk lindungi diri Anda dari dispute.</p>
        </div>
    @endif

    <div class="m-alert m-alert-info">
        <i class="bi bi-shield-fill-check"></i>
        <div><strong>Tips:</strong> Foto bukti membantu jika pelanggan komplain. Foto "sebelum" dan "sesudah" penting untuk membuktikan kerja Anda.</div>
    </div>
</div>

<style>
    .tipe-radio:checked + .tipe-card {
        border-color: var(--mitra-blue);
        background: var(--mitra-blue-tint);
        box-shadow: 0 0 0 2px rgba(0,90,169,0.15);
    }
</style>
@endsection
