@extends('layouts.pelanggan')

@push('styles')
<style>
    .notif-header { padding: 20px 16px 12px; }
    .notif-item { background: white; border-radius: 16px; padding: 14px 16px; margin-bottom: 10px; display: flex; gap: 12px; align-items: flex-start; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: .15s; }
    .notif-item.unread { border-left: 3px solid #002d72; }
    .notif-item.read { opacity: 0.7; }
    .notif-icon { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem; }
    .notif-icon.pesanan { background: #eff6ff; color: #002d72; }
    .notif-icon.topup { background: #f0fdf4; color: #16a34a; }
    .notif-icon.info { background: #fef9c3; color: #ca8a04; }
    .notif-judul { font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 3px; }
    .notif-pesan { font-size: 0.75rem; color: #64748b; line-height: 1.5; }
    .notif-waktu { font-size: 0.68rem; color: #94a3b8; margin-top: 5px; }
    .notif-dot { width: 8px; height: 8px; border-radius: 50%; background: #002d72; flex-shrink: 0; margin-top: 6px; }
    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; opacity: 0.4; }
    .back-btn { display: flex; align-items: center; gap: 8px; color: #002d72; font-weight: 700; font-size: 0.82rem; text-decoration: none; padding: 4px 0; }
</style>
@endpush

@section('content')
<div class="app-container">
    <div class="notif-header">
        <a href="{{ route('pelanggan.dashboard') }}" class="back-btn mb-3">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0" style="color:#1e293b;">Notifikasi</h5>
                <small class="text-muted">{{ $notifikasi->count() }} notifikasi</small>
            </div>
            @if($notifikasi->count())
                <span class="badge rounded-pill px-3 py-1" style="background:#eff6ff; color:#002d72; font-size:0.68rem; font-weight:700;">
                    Semua dibaca
                </span>
            @endif
        </div>
    </div>

    <div class="px-3 pb-4">
        @forelse($notifikasi as $notif)
            @php
                $iconClass = match($notif->tipe) {
                    'pesanan' => 'pesanan',
                    'topup'   => 'topup',
                    default   => 'info',
                };
                $iconName = match($notif->tipe) {
                    'pesanan' => 'bi-receipt',
                    'topup'   => 'bi-wallet2',
                    default   => 'bi-bell',
                };
                $waktu = \Carbon\Carbon::parse($notif->created_at)->diffForHumans();
            @endphp

            @if($notif->url)
                <a href="{{ $notif->url }}" class="text-decoration-none">
            @endif
            <div class="notif-item {{ $notif->is_read ? 'read' : 'unread' }}">
                <div class="notif-icon {{ $iconClass }}">
                    <i class="bi {{ $iconName }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="notif-judul">{{ $notif->judul }}</div>
                    <div class="notif-pesan">{{ $notif->pesan }}</div>
                    <div class="notif-waktu"><i class="bi bi-clock me-1"></i>{{ $waktu }}</div>
                </div>
                @if(!$notif->is_read)
                    <div class="notif-dot"></div>
                @endif
            </div>
            @if($notif->url)
                </a>
            @endif

        @empty
            <div class="empty-state">
                <i class="bi bi-bell-slash"></i>
                <div class="fw-bold mb-1" style="color:#475569; font-size:0.9rem;">Belum ada notifikasi</div>
                <div style="font-size:0.78rem;">Notifikasi akan muncul saat ada aktivitas pada akun Anda.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
