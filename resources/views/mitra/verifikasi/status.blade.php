@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

@php
    $totalRequired = $requiredKeys->count();
    $totalApproved = $uploaded->filter(fn($u) => $u->status === \App\Models\MitraVerifikasi::STATUS_APPROVED)->count();
    $totalPending  = $uploaded->filter(fn($u) => $u->status === \App\Models\MitraVerifikasi::STATUS_PENDING)->count();
    $percent = $totalRequired > 0 ? round(($totalApproved / $totalRequired) * 100) : 0;
@endphp

<div class="m-hero">
    <div class="text-center">
        <div class="m-hero-eyebrow">Status Verifikasi</div>
        <h1 class="m-hero-title" style="margin-top: var(--fib-1);">Lengkapi Dokumen Anda</h1>
        <div class="m-hero-stat-sub" style="margin-top: var(--fib-3);">
            {{ $totalApproved }} / {{ $totalRequired }} dokumen disetujui
        </div>
        <div style="max-width: var(--fib-8); margin: var(--fib-2) auto 0; height: var(--fib-2);
                    background: rgba(255,255,255,0.2); border-radius: var(--r-pill); overflow: hidden;">
            <div style="width: {{ $percent }}%; height: 100%;
                        background: linear-gradient(90deg, #fbbf24, #f59e0b);
                        border-radius: var(--r-pill); transition: width 0.4s;"></div>
        </div>
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif
    @if(session('warning'))<div class="m-alert m-alert-warn"><i class="bi bi-exclamation-circle-fill"></i>{{ session('warning') }}</div>@endif

    {{-- Info Role --}}
    <div class="m-card">
        <div class="m-card-body" style="display:flex; align-items:center; gap:var(--fib-3);">
            @if($mitra->role)
                <div style="width:var(--fib-6); height:var(--fib-6); border-radius:var(--r-md);
                            background: {{ ($mitra->role->icon_color ?? '#005aa9') . '15' }};
                            color: {{ $mitra->role->icon_color ?? '#005aa9' }};
                            display:flex; align-items:center; justify-content:center;
                            font-size:var(--t-lg); flex-shrink:0;">
                    <i class="bi {{ $mitra->role->icon ?? 'bi-shield-fill' }}"></i>
                </div>
                <div style="flex-grow:1;">
                    <div style="font-size:var(--t-xxs); color:var(--ink-soft); text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">Role Anda</div>
                    <div style="font-size:var(--t-base); font-weight:700; color:var(--ink);">{{ $mitra->role->name }}</div>
                    @if($mitra->role->description)
                        <div style="font-size:var(--t-xs); color:var(--ink-soft);">{{ $mitra->role->description }}</div>
                    @endif
                </div>
            @else
                <div style="color:#92400e;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Anda belum di-assign role. Hubungi admin Zasha untuk pemberian role.
                </div>
            @endif
        </div>
    </div>

    {{-- Daftar Dokumen --}}
    @if($requiredKeys->isEmpty())
        <div class="m-empty">
            <i class="bi bi-inbox m-empty-icon"></i>
            <h3 class="m-empty-title">Belum Ada Syarat Verifikasi</h3>
            <p class="m-empty-text">
                @if($mitra->role)
                    Role Anda belum punya syarat verifikasi. Hubungi admin.
                @else
                    Anda belum punya role aktif.
                @endif
            </p>
        </div>
    @else
        <h2 class="m-section-title">
            <i class="bi bi-list-check m-section-title-icon"></i>Dokumen yang Perlu Diunggah
        </h2>

        @foreach($requiredKeys as $req)
            @php
                $upload = $uploaded->get($req->verifikasi_key);
                $label  = \App\Models\Role::ALL_VERIFIKASI[$req->verifikasi_key] ?? $req->verifikasi_key;
                $status = $upload?->status;
                $isLink = in_array($req->verifikasi_key, ['github', 'portfolio']);
            @endphp

            <div class="m-card">
                <div class="m-card-body" style="display:flex; gap:var(--fib-3);">
                    <div style="width:var(--fib-5); height:var(--fib-5); border-radius:var(--r-md);
                                background:var(--mitra-blue-soft); color:var(--mitra-blue);
                                display:flex; align-items:center; justify-content:center;
                                font-size:var(--t-md); flex-shrink:0;">
                        <i class="bi {{ $isLink ? 'bi-link-45deg' : 'bi-file-earmark-text' }}"></i>
                    </div>
                    <div style="flex-grow:1; min-width:0;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:var(--fib-1);">
                            <div>
                                <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">{{ $label }}</div>
                                @if($req->wajib)
                                    <span style="background:#fee2e2; color:#991b1b; padding:2px var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">WAJIB</span>
                                @else
                                    <span style="background:var(--line); color:var(--ink-soft); padding:2px var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">OPSIONAL</span>
                                @endif
                            </div>
                            @if($status === \App\Models\MitraVerifikasi::STATUS_APPROVED)
                                <span style="background:#d1fae5; color:#065f46; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                                    <i class="bi bi-check-circle-fill"></i> Disetujui
                                </span>
                            @elseif($status === \App\Models\MitraVerifikasi::STATUS_PENDING)
                                <span style="background:#fef3c7; color:#92400e; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                                    <i class="bi bi-clock-fill"></i> Review
                                </span>
                            @elseif($status === \App\Models\MitraVerifikasi::STATUS_DITOLAK)
                                <span style="background:#fee2e2; color:#991b1b; padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                                    <i class="bi bi-x-circle-fill"></i> Ditolak
                                </span>
                            @else
                                <span style="background:var(--line); color:var(--ink-soft); padding:var(--fib-1) var(--fib-2); border-radius:var(--r-pill); font-size:var(--t-xxs); font-weight:700;">
                                    <i class="bi bi-circle"></i> Belum
                                </span>
                            @endif
                        </div>

                        @if($upload && $upload->catatan)
                            <div class="m-alert m-alert-warn" style="margin-top:var(--fib-2); margin-bottom:var(--fib-2);">
                                <strong>Catatan admin:</strong> {{ $upload->catatan }}
                            </div>
                        @endif

                        @if($upload && $upload->file_path)
                            <div style="margin-top:var(--fib-2);">
                                @if(filter_var($upload->file_path, FILTER_VALIDATE_URL))
                                    <a href="{{ $upload->file_path }}" target="_blank" style="font-size:var(--t-xs); color:var(--mitra-blue); text-decoration:none;">
                                        <i class="bi bi-box-arrow-up-right"></i>{{ \Illuminate\Support\Str::limit($upload->file_path, 50) }}
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank" style="font-size:var(--t-xs); color:var(--mitra-blue); text-decoration:none;">
                                        <i class="bi bi-eye-fill"></i> Lihat dokumen sebelumnya
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if($status !== \App\Models\MitraVerifikasi::STATUS_APPROVED)
                            <form action="{{ route('mitra.verifikasi.upload') }}" method="POST"
                                  enctype="multipart/form-data" style="margin-top:var(--fib-3);">
                                @csrf
                                <input type="hidden" name="verifikasi_key" value="{{ $req->verifikasi_key }}">
                                <div style="display:flex; gap:var(--fib-1);">
                                    @if($isLink)
                                        <input type="url" name="link" class="m-form-input" style="flex-grow:1;"
                                               placeholder="https://github.com/username"
                                               value="{{ $upload && filter_var($upload->file_path, FILTER_VALIDATE_URL) ? $upload->file_path : '' }}"
                                               required>
                                    @else
                                        <input type="file" name="file" class="m-form-input" style="flex-grow:1;"
                                               accept="image/jpeg,image/png,application/pdf" required>
                                    @endif
                                    <button type="submit" class="m-btn-primary" style="flex-shrink:0; padding:var(--fib-2) var(--fib-3);">
                                        <i class="bi bi-cloud-upload"></i>
                                    </button>
                                </div>
                                @if(!$isLink)
                                    <div class="m-form-help">JPG/PNG/PDF, maks 5MB</div>
                                @endif
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        @if($totalRequired > 0 && $totalRequired === ($totalApproved + $totalPending))
            <div class="m-alert m-alert-info" style="text-align:center; justify-content:center;">
                <i class="bi bi-info-circle-fill"></i>
                @if($totalPending > 0)
                    Semua dokumen sudah dikirim. Tinggal tunggu review admin Zasha.
                @else
                    Semua dokumen sudah disetujui! Tunggu admin mengaktifkan akun Anda.
                @endif
            </div>
        @endif
    @endif

    <div style="text-align:center; margin-top:var(--fib-4);">
        <form action="{{ route('mitra.logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" style="background:none; border:none; color:var(--ink-soft); font-size:var(--t-xs); text-decoration:none; cursor:pointer;">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </form>
    </div>
</div>
@endsection
