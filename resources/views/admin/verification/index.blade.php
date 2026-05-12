@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')

<style>
    .btn-status-action {
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 0.7rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: all 0.15s;
        opacity: 0.85;
    }
    .btn-status-action:hover { opacity: 1; transform: translateY(-1px); }
    .btn-status-action.success { background: #10b981; color: white; }
    .btn-status-action.process { background: #3b82f6; color: white; }
    .btn-status-action.danger  { background: #ef4444; color: white; }

    .diff-inline {
        display: inline-flex; flex-wrap: wrap; gap: 4px; align-items: center;
        font-size: 0.72rem;
    }
    .diff-old {
        background: #fee2e2; color: #991b1b;
        padding: 1px 7px; border-radius: 4px;
        text-decoration: line-through;
        text-decoration-color: #991b1b66;
    }
    .diff-new {
        background: #d1fae5; color: #065f46;
        padding: 1px 7px; border-radius: 4px;
        font-weight: 700;
    }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-shield-lock-fill"></i> Verifikasi Dokumen</h4>
        <div class="zasha-page-subtitle">Pengajuan perubahan data & pendaftaran mitra baru — cek sebelum approve.</div>
    </div>
    <a href="{{ route('admin.verification.index') }}" class="btn btn-light rounded-pill px-3 border small fw-bold">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
    </a>
</div>

@if(session('notif_verif'))
    <div class="alert alert-success rounded-3 small">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('notif_verif') }}
    </div>
@endif

<div class="alert alert-info rounded-3 small mb-3" style="background:#eff6ff; border-color:#bfdbfe; color:#1e40af;">
    <i class="bi bi-info-circle-fill me-1"></i>
    <strong>Double-check:</strong> Cocokkan data baru (warna hijau) dengan dokumen yang dikirim mitra di WhatsApp sebelum klik approve.
</div>

{{-- ── DOKUMEN VERIFIKASI (sistem role-based) ────────── --}}
@if(($list_dokumen ?? collect())->isNotEmpty())
<div class="zasha-card mb-3">
    <div class="zasha-list-header">
        <h6><i class="bi bi-file-earmark-check-fill"></i> Dokumen Verifikasi Pending</h6>
        <span class="badge-count">{{ $list_dokumen->sum('pending_count') }}</span>
    </div>

    @foreach($list_dokumen as $d)
        <div class="zasha-row">
            <div class="zasha-row-icon" style="background:{{ ($d->role->icon_color ?? '#005aa9') . '15' }};
                                                color:{{ $d->role->icon_color ?? '#005aa9' }};">
                <i class="bi {{ $d->role->icon ?? 'bi-shield-fill' }}"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    {{ $d->nama_panggilan }}
                    @if($d->role)
                        <span class="zasha-status-badge ms-1"
                              style="font-size:0.6rem;background:{{ ($d->role->icon_color ?? '#005aa9') . '20' }};
                                     color:{{ $d->role->icon_color ?? '#005aa9' }};">
                            {{ $d->role->name }}
                        </span>
                    @endif
                </div>
                <div class="zasha-row-meta">
                    <span><i class="bi bi-file-earmark-text"></i> {{ $d->pending_count }} dokumen menunggu review</span>
                    @if($d->no_wa)
                        <span><i class="bi bi-whatsapp text-success"></i> {{ $d->no_wa }}</span>
                    @endif
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <a href="{{ route('admin.verification.review', $d->id_mitra) }}"
                   class="btn-status-action process">
                    <i class="bi bi-eye-fill"></i> Review
                </a>
            </div>
        </div>
    @endforeach
</div>
@endif

<div class="zasha-card">
    <div class="zasha-list-header">
        <h6><i class="bi bi-hourglass-split"></i> Antrian Verifikasi Profil</h6>
        <span class="badge-count">{{ $list_driver->count() + $list_mitra->count() }}</span>
    </div>

    @if($list_driver->isEmpty() && $list_mitra->isEmpty())
        <div class="zasha-empty">
            <i class="bi bi-cup-hot"></i>
            <div class="zasha-empty-title">Semua aman!</div>
            <div class="zasha-empty-sub">Tidak ada pengajuan verifikasi yang tertunda.</div>
        </div>
    @endif

    {{-- ── DRIVER JASTIP ───────────────────────────── --}}
    @foreach($list_driver as $r)
        <div class="zasha-row">
            <div class="zasha-row-icon success">
                <i class="bi bi-truck"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    {{ $r->nama_panggilan }}
                    <span class="zasha-status-badge success ms-1" style="font-size:0.6rem;">Driver Jastip</span>
                </div>
                <div class="zasha-row-meta">
                    @if(!empty($r->nama_asli_baru))
                        <span class="diff-inline">
                            <i class="bi bi-pencil-fill"></i>
                            <span class="diff-old">{{ $r->nama_asli }}</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="diff-new">{{ $r->nama_asli_baru }}</span>
                        </span>
                    @else
                        <span><i class="bi bi-person"></i> {{ $r->nama_asli }}</span>
                    @endif
                    <span><i class="bi bi-car-front-fill"></i> {{ $r->plat_nomor }}</span>
                    <span><i class="bi bi-whatsapp text-success"></i> {{ $r->no_wa }}</span>
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <form action="{{ route('admin.verification.approve') }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Setujui perubahan data untuk {{ $r->nama_panggilan }}?')">
                    @csrf
                    <input type="hidden" name="target_id" value="{{ $r->id_mitra }}">
                    <input type="hidden" name="account_type" value="driver">
                    <button type="submit" class="btn-status-action success">
                        <i class="bi bi-check-lg"></i> Approve
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    {{-- ── MITRA LAYANAN ────────────────────────────── --}}
    @foreach($list_mitra as $m)
        @php
            $hasChange = !empty($m->no_wa_baru) || !empty($m->nama_asli_baru) || !empty($m->plat_pengajuan) || !empty($m->alamat_pengajuan);
        @endphp
        <div class="zasha-row">
            <div class="zasha-row-icon {{ $hasChange ? 'warning' : '' }}">
                <i class="bi {{ $hasChange ? 'bi-pencil-square' : 'bi-person-plus-fill' }}"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    {{ $m->nama_panggilan ?? $m->nama_asli ?? 'Unknown' }}
                    @if($hasChange)
                        <span class="zasha-status-badge pending ms-1" style="font-size:0.6rem;">Update Data</span>
                    @else
                        <span class="zasha-status-badge process ms-1" style="font-size:0.6rem;">Pendaftaran Baru</span>
                    @endif
                </div>
                <div class="zasha-row-meta">
                    @if(!empty($m->no_wa_baru))
                        <span class="diff-inline">
                            <i class="bi bi-whatsapp"></i>
                            <span class="diff-old">{{ $m->no_wa }}</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="diff-new">{{ $m->no_wa_baru }}</span>
                        </span>
                    @elseif(!empty($m->plat_pengajuan))
                        <span class="diff-inline">
                            <i class="bi bi-car-front-fill"></i>
                            <span class="diff-old">{{ $m->plat_nomor }}</span>
                            <i class="bi bi-arrow-right"></i>
                            <span class="diff-new">{{ $m->plat_pengajuan }}</span>
                        </span>
                    @else
                        <span><i class="bi bi-whatsapp text-success"></i> {{ $m->no_wa }}</span>
                        @if($m->jenis_kendaraan)
                            <span><i class="bi bi-car-front"></i> {{ $m->jenis_kendaraan }} · {{ $m->plat_nomor }}</span>
                        @endif
                    @endif
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <form action="{{ route('admin.verification.approve') }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Setujui verifikasi data mitra {{ $m->nama_panggilan ?? $m->nama_asli }}?')">
                    @csrf
                    <input type="hidden" name="target_id" value="{{ $m->id_mitra }}">
                    <input type="hidden" name="account_type" value="mitra">
                    <button type="submit" class="btn-status-action process">
                        <i class="bi bi-check-lg"></i> Setujui
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection
