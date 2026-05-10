@extends('layouts.pelanggan')

@section('content')

{{-- ── HEADER ────────────────────────────────────── --}}
<div style="background:linear-gradient(145deg,#001d4d,#002d72);padding:21px;
            position:sticky;top:0;z-index:100;">
    <div style="display:flex;align-items:center;gap:13px;">
        <a href="{{ route('pelanggan.dashboard') }}"
           style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.1);
                  display:flex;align-items:center;justify-content:center;text-decoration:none;flex-shrink:0;">
            <i class="bi bi-arrow-left" style="color:white;font-size:16px;"></i>
        </a>
        <div>
            <div style="font-size:17px;font-weight:800;color:white;">Notifikasi</div>
            <div style="font-size:10px;color:rgba(255,255,255,.5);">{{ $notifikasi->count() }} total</div>
        </div>
    </div>
</div>

{{-- ── LIST ─────────────────────────────────────── --}}
<div style="padding:13px 21px 34px;">

    @forelse($notifikasi as $notif)
        @php
            $icon  = match($notif->tipe) { 'pesanan'=>'bi-receipt-cutoff', 'topup'=>'bi-wallet-fill', default=>'bi-bell-fill' };
            $ibg   = match($notif->tipe) { 'pesanan'=>'#eff6ff', 'topup'=>'#d1fae5', default=>'#fef9c3' };
            $iclr  = match($notif->tipe) { 'pesanan'=>'#002d72', 'topup'=>'#065f46', default=>'#b45309' };
            $waktu = \Carbon\Carbon::parse($notif->created_at)->diffForHumans();
        @endphp

        @if($notif->url)
            <a href="{{ $notif->url }}" style="text-decoration:none;">
        @endif
        <div style="background:white;border-radius:21px;padding:16px;margin-bottom:8px;
                    display:flex;gap:13px;align-items:flex-start;
                    box-shadow:0 2px 12px rgba(0,45,114,.06);
                    {{ !$notif->is_read ? 'border-left:3px solid var(--gold);' : 'opacity:.7;' }}">
            <div style="width:44px;height:44px;border-radius:13px;background:{{ $ibg }};
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi {{ $icon }}" style="color:{{ $iclr }};font-size:20px;"></i>
            </div>
            <div style="flex:1;">
                <div style="font-size:13px;font-weight:800;color:var(--text-main);margin-bottom:4px;">
                    {{ $notif->judul }}
                </div>
                <div style="font-size:11px;color:var(--text-muted);line-height:1.5;margin-bottom:5px;">
                    {{ $notif->pesan }}
                </div>
                <div style="font-size:10px;color:var(--text-faint);display:flex;align-items:center;gap:4px;">
                    <i class="bi bi-clock"></i>{{ $waktu }}
                </div>
            </div>
            @if(!$notif->is_read)
                <div style="width:8px;height:8px;border-radius:50%;background:var(--gold);
                            flex-shrink:0;margin-top:5px;"></div>
            @endif
        </div>
        @if($notif->url)
            </a>
        @endif

    @empty
        <div style="text-align:center;padding:55px 0;color:var(--text-faint);">
            <i class="bi bi-bell-slash" style="font-size:44px;display:block;margin-bottom:13px;opacity:.3;"></i>
            <div style="font-size:14px;font-weight:700;margin-bottom:8px;color:var(--text-muted);">
                Belum ada notifikasi
            </div>
            <div style="font-size:12px;">Notifikasi muncul saat ada aktivitas di akunmu.</div>
        </div>
    @endforelse

</div>
@endsection
