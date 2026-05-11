@extends('layouts.pelanggan')

@section('content')
{{-- Header --}}
<div style="background: linear-gradient(135deg, #002d72 0%, #0047b3 60%, #005dd6 100%);
            padding: 21px; position: relative; display: flex; align-items: center; gap: 13px;">
    <a href="{{ route('pelanggan.dashboard') }}" style="width: 42px; height: 42px; border-radius: 13px;
                background: rgba(255,255,255,.12); display: flex; align-items: center; justify-content: center;
                border: 1px solid rgba(255,255,255,.2); text-decoration: none; flex-shrink: 0;">
        <i class="bi bi-chevron-left" style="color: white; font-size: 18px;"></i>
    </a>
    <h4 style="color: white; font-weight: 800; font-size: 18px; margin: 0; flex: 1;">{{ $judul }}</h4>
</div>

{{-- Brand Cards --}}
<div style="padding: 21px;">
    @forelse($kategoris as $kat)
        <a href="{{ route('pelanggan.ppob.produk', $kat->kode) }}" class="z-card"
           style="padding: 13px; display: flex; align-items: center; gap: 13px; text-decoration: none;
                   margin-bottom: 8px;">
            @if($kat->thumbnail)
                <img src="{{ asset('storage/'.$kat->thumbnail) }}" style="width: 44px; height: 44px;
                    border-radius: 13px; object-fit: cover;">
            @else
                <div style="width: 44px; height: 44px; border-radius: 13px; background: var(--blue-pale);
                            display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-box-fill" style="color: var(--blue-deep); font-size: 21px;"></i>
                </div>
            @endif
            <div style="flex: 1;">
                <div style="font-size: 13px; font-weight: 800; color: var(--text-main);">
                    {{ $kat->sub_nama ?? $kat->nama }}
                </div>
                <div style="font-size: 10px; color: var(--text-muted);">
                    {{ $kat->layanans_count }} produk
                </div>
            </div>
            <i class="bi bi-chevron-right" style="color: var(--text-faint);"></i>
        </a>
    @empty
        <div style="text-align: center; padding: 55px 21px; color: var(--text-muted);">
            <i class="bi bi-inbox" style="font-size: 34px; display: block; margin-bottom: 13px;"></i>
            <div style="font-size: 13px; font-weight: 700;">Belum ada produk</div>
            <div style="font-size: 11px; margin-top: 8px;">Kategori {{ $judul }} sedang tidak tersedia.</div>
        </div>
    @endempty
</div>

@endsection
