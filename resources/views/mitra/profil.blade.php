@extends('layouts.mitra')

@section('content')
{{-- Hero with avatar — golden ratio: hero height : avatar overlap = φ:1 --}}
<div style="background: linear-gradient(135deg, var(--mitra-green) 0%, var(--mitra-green-light) 100%); padding: var(--fib-5) var(--fib-4) var(--fib-7); position: relative; overflow: hidden;">
    {{-- Decorative golden circles --}}
    <div style="position:absolute;top:calc(var(--fib-6)*-1);right:calc(var(--fib-6)*-1);width:var(--fib-7);height:var(--fib-7);background:rgba(212,175,55,0.18);border-radius:50%;"></div>
    <div style="position:absolute;bottom:calc(var(--fib-4)*-1);left:calc(var(--fib-5)*-1);width:var(--fib-6);height:var(--fib-6);background:rgba(255,255,255,0.06);border-radius:50%;"></div>

    <div style="position: relative; z-index: 2; text-align: center;">
        <div class="label-up" style="color: rgba(255,255,255,0.7); margin-bottom: var(--fib-2);">Profil Mitra</div>
        <h5 class="fw-bold m-0 text-white t-lg" style="letter-spacing: -0.01em;">Akun Saya</h5>
    </div>
</div>

{{-- Avatar overlap (golden ratio: 89/55 ≈ φ) --}}
<div class="page-pad stack-3" style="margin-top: calc(var(--fib-6) * -1);">

    <div class="text-center" style="position: relative; z-index: 3;">
        <div class="avatar-89 mx-auto" style="background: var(--surface); border: var(--fib-1) solid var(--surface); display:inline-flex; align-items:center; justify-content:center; box-shadow: 0 var(--fib-2) var(--fib-5) rgba(10,92,54,0.15); border-radius: 50%; overflow: hidden;">
            @if($mitra->foto_mitra)
                <img src="{{ asset('storage/' . $mitra->foto_mitra) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            @else
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #e8f5e9, var(--mitra-gold-soft)); border-radius: 50%; display:flex; align-items:center; justify-content:center;">
                    <i class="bi bi-person-fill" style="font-size: var(--fib-5); color: var(--mitra-green);"></i>
                </div>
            @endif
        </div>
    </div>

    <div class="text-center stack-3">
        <div>
            <h5 class="fw-bold mb-0 allow-select t-lg" style="letter-spacing:-0.01em;">{{ $mitra->nama_asli ?? $mitra->nama_panggilan ?? 'Mitra' }}</h5>
            @if($mitra->nama_panggilan && $mitra->nama_asli && $mitra->nama_panggilan !== $mitra->nama_asli)
                <div class="t-xs" style="color: var(--ink-soft); margin-top: var(--fib-1);">@{{ $mitra->nama_panggilan }}</div>
            @endif
        </div>
        <div class="d-flex justify-content-center" style="gap: var(--fib-2);">
            <span class="allow-select" style="background: var(--mitra-gold-soft); color: #8b6914; padding: var(--fib-1) var(--fib-3); border-radius: var(--r-pill); font-size: var(--t-xxs); font-weight: 800; letter-spacing: 0.05em;">
                <i class="bi bi-stars me-1"></i>{{ $mitra->id_mitra ?? '-' }}
            </span>
            <span class="{{ ($mitra->status_mitra ?? '') === 'aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
                {{ ucfirst($mitra->status_mitra ?? 'offline') }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success small mb-0" style="border-radius: var(--r-md); padding: var(--fib-2) var(--fib-3); font-size: var(--t-xs);">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Info Akun --}}
    <div class="card-custom" style="padding: var(--fib-4);">
        <div class="d-flex justify-content-between align-items-center" style="margin-bottom: var(--fib-3);">
            <h6 class="label-up mb-0">Informasi Akun</h6>
            <a href="#" class="t-xs fw-bold" style="color: var(--mitra-green); text-decoration: none;">
                <i class="bi bi-pencil-square"></i> Edit
            </a>
        </div>

        <div class="stack-3">
            <div class="d-flex justify-content-between align-items-center" style="padding-bottom: var(--fib-3); border-bottom: 1px solid var(--line);">
                <div>
                    <div class="t-xxs label-up">No. WhatsApp</div>
                    <div class="t-sm fw-semibold allow-select mt-1">+{{ $mitra->no_wa ?? '-' }}</div>
                </div>
                <i class="bi bi-whatsapp" style="color: #25d366; font-size: var(--t-md);"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center" style="padding-bottom: var(--fib-3); border-bottom: 1px solid var(--line);">
                <div>
                    <div class="t-xxs label-up">Kategori</div>
                    <div class="t-sm fw-semibold mt-1">
                        {{ $mitra->kategori_kode?->label() ?? $mitra->id_kategori ?? 'Belum dipilih' }}
                    </div>
                </div>
                <i class="bi bi-tag-fill" style="color: var(--mitra-gold); font-size: var(--t-md);"></i>
            </div>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="t-xxs label-up">Verifikasi</div>
                    <div class="t-sm fw-semibold mt-1">
                        @if($mitra->status_verifikasi instanceof \App\Enums\MitraStatus)
                            {{ $mitra->status_verifikasi->label() }}
                        @else
                            {{ ucfirst(str_replace('_', ' ', $mitra->status_verifikasi ?? '-')) }}
                        @endif
                    </div>
                </div>
                <i class="bi bi-shield-check" style="color: var(--mitra-green); font-size: var(--t-md);"></i>
            </div>
        </div>
    </div>

    {{-- Tarif Layanan --}}
    <div class="card-custom" style="padding: var(--fib-4);">
        <h6 class="label-up mb-0" style="margin-bottom: var(--fib-3);">Tarif Layanan</h6>
        <div class="row g-3">
            <div class="col-6">
                <div style="padding: var(--fib-3); background: linear-gradient(135deg, var(--mitra-gold-soft), #fff); border-radius: var(--r-md);">
                    <div class="t-xxs label-up" style="margin-bottom: var(--fib-1);">Per Jam</div>
                    <div class="fw-bold t-sm" style="color: var(--mitra-green);">
                        Rp {{ number_format($mitra->tarif_per_jam ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div style="padding: var(--fib-3); background: linear-gradient(135deg, #ecfdf5, #fff); border-radius: var(--r-md);">
                    <div class="t-xxs label-up" style="margin-bottom: var(--fib-1);">Per Hari</div>
                    <div class="fw-bold t-sm" style="color: var(--mitra-green);">
                        Rp {{ number_format($mitra->tarif_per_hari ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="stack-3">
        <a href="#" class="menu-tile">
            <div class="menu-tile-icon" style="background: #ecfdf5; color: var(--mitra-green);">
                <i class="bi bi-shield-check"></i>
            </div>
            <div style="flex: 1;">
                <div class="fw-bold t-sm">Dokumen Verifikasi</div>
                <div class="t-xxs" style="color: var(--ink-soft);">Upload KTP, Foto, dll</div>
            </div>
            <i class="bi bi-chevron-right" style="color: var(--ink-soft);"></i>
        </a>
        <a href="#" class="menu-tile">
            <div class="menu-tile-icon" style="background: var(--mitra-gold-soft); color: var(--mitra-gold);">
                <i class="bi bi-bank"></i>
            </div>
            <div style="flex: 1;">
                <div class="fw-bold t-sm">Rekening Bank</div>
                <div class="t-xxs" style="color: var(--ink-soft);">Untuk pencairan saldo</div>
            </div>
            <i class="bi bi-chevron-right" style="color: var(--ink-soft);"></i>
        </a>
        <a href="#" class="menu-tile">
            <div class="menu-tile-icon" style="background: #f3e8ff; color: #7c3aed;">
                <i class="bi bi-question-circle"></i>
            </div>
            <div style="flex: 1;">
                <div class="fw-bold t-sm">Bantuan & FAQ</div>
                <div class="t-xxs" style="color: var(--ink-soft);">Panduan & support</div>
            </div>
            <i class="bi bi-chevron-right" style="color: var(--ink-soft);"></i>
        </a>
    </div>

    {{-- Logout --}}
    <form action="{{ route('mitra.logout') }}" method="POST" class="mb-0">
        @csrf
        <button type="submit" class="btn w-100" style="background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: var(--r-pill); font-weight: 700; padding: var(--fib-3); font-size: var(--t-sm);">
            <i class="bi bi-box-arrow-right me-2"></i>Keluar
        </button>
    </form>
</div>
@endsection
