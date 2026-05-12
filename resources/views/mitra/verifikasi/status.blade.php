@extends('layouts.mitra')

@section('content')
@php
    $totalRequired = $requiredKeys->count();
    $totalApproved = $uploaded->filter(fn($u) => $u->status === \App\Models\MitraVerifikasi::STATUS_APPROVED)->count();
    $totalPending  = $uploaded->filter(fn($u) => $u->status === \App\Models\MitraVerifikasi::STATUS_PENDING)->count();
    $totalDitolak  = $uploaded->filter(fn($u) => $u->status === \App\Models\MitraVerifikasi::STATUS_DITOLAK)->count();
    $percent = $totalRequired > 0 ? round(($totalApproved / $totalRequired) * 100) : 0;
@endphp

{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="text-center">
        <div style="font-size: .75rem; opacity: .8; margin-bottom: 6px;">STATUS VERIFIKASI</div>
        <h5 class="fw-bold m-0">Lengkapi Dokumen Anda</h5>
        <div class="mt-3" style="font-size: .8rem; opacity: .9;">
            {{ $totalApproved }} / {{ $totalRequired }} dokumen disetujui
        </div>
        <div class="mt-2 mx-auto" style="max-width: 240px; height: 8px; background: rgba(255,255,255,.2); border-radius: 999px; overflow: hidden;">
            <div style="width: {{ $percent }}%; height: 100%; background: #d4af37; border-radius: 999px; transition: width .4s;"></div>
        </div>
    </div>
</div>

<div class="page-pad" style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning rounded-3 shadow-sm">{{ session('warning') }}</div>
    @endif

    {{-- Info Role --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body d-flex align-items-center gap-3">
            @if($mitra->role)
                <div style="width: 48px; height: 48px; border-radius: 12px;
                            background: {{ ($mitra->role->icon_color ?? '#005aa9') . '15' }};
                            color: {{ $mitra->role->icon_color ?? '#005aa9' }};
                            display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                    <i class="bi {{ $mitra->role->icon ?? 'bi-shield-fill' }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="text-muted small">Role Anda</div>
                    <div class="fw-bold">{{ $mitra->role->name }}</div>
                    @if($mitra->role->description)
                        <div class="text-muted small">{{ $mitra->role->description }}</div>
                    @endif
                </div>
            @else
                <div class="text-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Anda belum di-assign role. Hubungi admin Zasha untuk pemberian role.
                </div>
            @endif
        </div>
    </div>

    {{-- Daftar Dokumen --}}
    @if($requiredKeys->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #cbd5e1;"></i>
                <h6 class="mt-3 fw-bold">Belum Ada Syarat Verifikasi</h6>
                <p class="text-muted small mb-0">
                    @if($mitra->role)
                        Role Anda belum punya syarat verifikasi. Hubungi admin.
                    @else
                        Anda belum punya role aktif.
                    @endif
                </p>
            </div>
        </div>
    @else
        <h6 class="fw-bold mt-3 mb-2" style="color: #1e293b;">
            Dokumen yang Perlu Diunggah
        </h6>

        @foreach($requiredKeys as $req)
            @php
                $upload = $uploaded->get($req->verifikasi_key);
                $label  = \App\Models\Role::ALL_VERIFIKASI[$req->verifikasi_key] ?? $req->verifikasi_key;
                $status = $upload?->status;
                $isLink = in_array($req->verifikasi_key, ['github', 'portfolio']);
            @endphp

            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #eff6ff;
                                    color: #005aa9; display: flex; align-items: center; justify-content: center;
                                    font-size: 1.2rem; flex-shrink: 0;">
                            <i class="bi {{ $isLink ? 'bi-link-45deg' : 'bi-file-earmark-text' }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                                <div>
                                    <div class="fw-bold" style="color: #1e293b;">{{ $label }}</div>
                                    @if($req->wajib)
                                        <span class="badge bg-danger" style="font-size: .65rem;">WAJIB</span>
                                    @else
                                        <span class="badge bg-secondary" style="font-size: .65rem;">Opsional</span>
                                    @endif
                                </div>
                                @if($status === \App\Models\MitraVerifikasi::STATUS_APPROVED)
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i>Disetujui
                                    </span>
                                @elseif($status === \App\Models\MitraVerifikasi::STATUS_PENDING)
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        <i class="bi bi-clock-fill me-1"></i>Menunggu Review
                                    </span>
                                @elseif($status === \App\Models\MitraVerifikasi::STATUS_DITOLAK)
                                    <span class="badge bg-danger rounded-pill px-3 py-2">
                                        <i class="bi bi-x-circle-fill me-1"></i>Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted rounded-pill px-3 py-2">
                                        <i class="bi bi-circle me-1"></i>Belum Upload
                                    </span>
                                @endif
                            </div>

                            @if($upload && $upload->catatan)
                                <div class="alert alert-warning mt-2 mb-2 py-2 px-3" style="font-size: .8rem;">
                                    <strong>Catatan admin:</strong> {{ $upload->catatan }}
                                </div>
                            @endif

                            @if($upload && $upload->file_path)
                                <div class="mt-2">
                                    @if(filter_var($upload->file_path, FILTER_VALIDATE_URL))
                                        <a href="{{ $upload->file_path }}" target="_blank" class="small text-decoration-none">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ \Illuminate\Support\Str::limit($upload->file_path, 50) }}
                                        </a>
                                    @else
                                        <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank" class="small text-decoration-none">
                                            <i class="bi bi-eye-fill me-1"></i>Lihat dokumen sebelumnya
                                        </a>
                                    @endif
                                </div>
                            @endif

                            {{-- Form upload --}}
                            @if($status !== \App\Models\MitraVerifikasi::STATUS_APPROVED)
                                <form action="{{ route('mitra.verifikasi.upload') }}" method="POST"
                                      enctype="multipart/form-data" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="verifikasi_key" value="{{ $req->verifikasi_key }}">

                                    @if($isLink)
                                        <div class="input-group input-group-sm">
                                            <input type="url" name="link" class="form-control rounded-start"
                                                   placeholder="https://github.com/username atau link portfolio"
                                                   value="{{ $upload && filter_var($upload->file_path, FILTER_VALIDATE_URL) ? $upload->file_path : '' }}"
                                                   required>
                                            <button type="submit" class="btn btn-primary rounded-end px-3">
                                                <i class="bi bi-cloud-upload"></i>
                                            </button>
                                        </div>
                                    @else
                                        <div class="input-group input-group-sm">
                                            <input type="file" name="file" class="form-control"
                                                   accept="image/jpeg,image/png,application/pdf" required>
                                            <button type="submit" class="btn btn-primary px-3">
                                                <i class="bi bi-cloud-upload"></i> Upload
                                            </button>
                                        </div>
                                        <div class="form-text" style="font-size: .7rem;">
                                            JPG/PNG/PDF, maks 5MB
                                        </div>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Info kalau semua sudah diupload --}}
        @if($totalRequired > 0 && $totalRequired === ($totalApproved + $totalPending))
            <div class="alert alert-info rounded-4 mt-3 text-center" style="border: none; background: #eff6ff; color: #005aa9;">
                <i class="bi bi-info-circle-fill me-2"></i>
                @if($totalPending > 0)
                    Semua dokumen sudah dikirim. Tinggal tunggu review admin Zasha.
                @else
                    Semua dokumen sudah disetujui! Tunggu admin mengaktifkan akun Anda.
                @endif
            </div>
        @endif
    @endif

    {{-- Logout --}}
    <div class="text-center mt-4">
        <form action="{{ route('mitra.logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link text-muted small text-decoration-none">
                <i class="bi bi-box-arrow-right me-1"></i>Keluar
            </button>
        </form>
    </div>
</div>
@endsection
