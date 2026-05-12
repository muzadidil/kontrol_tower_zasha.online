@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')

<style>
    .dok-card { background:#fff; border-radius:14px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,.04); margin-bottom:14px; }
    .dok-card.approved { border-left:4px solid #10b981; }
    .dok-card.pending  { border-left:4px solid #f59e0b; }
    .dok-card.ditolak  { border-left:4px solid #ef4444; }
    .dok-card.kosong   { border-left:4px solid #cbd5e1; background:#f8fafc; }

    .file-preview { max-height: 220px; width: 100%; object-fit: contain; border-radius: 10px; background: #f1f5f9; }
    .file-link { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#eff6ff; color:#005aa9;
                 border-radius:999px; text-decoration:none; font-size:.8rem; font-weight:600; }
    .file-link:hover { background:#dbeafe; }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <a href="{{ route('admin.verification.index') }}" class="text-muted text-decoration-none small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Antrian
        </a>
        <h4 class="mt-1"><i class="bi bi-file-earmark-check-fill"></i> Review Dokumen Mitra</h4>
        <div class="zasha-page-subtitle">
            {{ $mitra->nama_panggilan }} ({{ $mitra->nama_asli ?? '—' }})
            @if($mitra->role)
                · <span style="color:{{ $mitra->role->icon_color ?? '#005aa9' }}">
                    <i class="bi {{ $mitra->role->icon ?? 'bi-shield-fill' }}"></i>
                    {{ $mitra->role->name }}
                </span>
            @endif
        </div>
    </div>
    <a href="{{ route('admin.mitra.edit', $mitra->id_mitra) }}" class="btn btn-light rounded-pill px-3 border small fw-bold">
        <i class="bi bi-pencil me-1"></i> Edit Mitra
    </a>
</div>

@if(session('notif_verif'))
    <div class="alert alert-success rounded-3 small">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('notif_verif') }}
    </div>
@endif

@if($requiredKeys->isEmpty())
    <div class="zasha-card" style="padding:30px; text-align:center;">
        <i class="bi bi-exclamation-triangle" style="font-size:2.5rem; color:#f59e0b;"></i>
        <h6 class="mt-3 fw-bold">Mitra Belum Punya Role</h6>
        <div class="text-muted small">
            Assign role terlebih dahulu agar mitra tahu dokumen apa yang harus diunggah.
        </div>
        <a href="{{ route('admin.mitra.edit', $mitra->id_mitra) }}" class="btn btn-primary rounded-pill px-4 mt-3 small">
            <i class="bi bi-shield-plus me-1"></i>Assign Role
        </a>
    </div>
@else
    @foreach($requiredKeys as $req)
        @php
            $dok = $uploaded->get($req->verifikasi_key);
            $label = \App\Models\Role::ALL_VERIFIKASI[$req->verifikasi_key] ?? $req->verifikasi_key;
            $status = $dok?->status;
            $cardClass = match(true) {
                !$dok => 'kosong',
                $status === \App\Models\MitraVerifikasi::STATUS_APPROVED => 'approved',
                $status === \App\Models\MitraVerifikasi::STATUS_DITOLAK => 'ditolak',
                default => 'pending',
            };
            $isUrl = $dok && filter_var($dok->file_path, FILTER_VALIDATE_URL);
        @endphp

        <div class="dok-card {{ $cardClass }}">
            <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                <div>
                    <div class="fw-bold" style="color:#1e293b;">{{ $label }}</div>
                    <div class="d-flex gap-2 align-items-center mt-1">
                        @if($req->wajib)
                            <span class="badge bg-danger" style="font-size:.65rem;">WAJIB</span>
                        @else
                            <span class="badge bg-secondary" style="font-size:.65rem;">Opsional</span>
                        @endif

                        @if(!$dok)
                            <span class="text-muted small fst-italic">Belum diunggah</span>
                        @elseif($status === \App\Models\MitraVerifikasi::STATUS_APPROVED)
                            <span class="badge bg-success rounded-pill px-3">
                                <i class="bi bi-check-circle-fill me-1"></i>Disetujui
                            </span>
                        @elseif($status === \App\Models\MitraVerifikasi::STATUS_DITOLAK)
                            <span class="badge bg-danger rounded-pill px-3">
                                <i class="bi bi-x-circle-fill me-1"></i>Ditolak
                            </span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3">
                                <i class="bi bi-clock-fill me-1"></i>Menunggu Review
                            </span>
                        @endif
                    </div>
                </div>
                @if($dok && $dok->updated_at)
                    <small class="text-muted">
                        Upload: {{ $dok->updated_at->diffForHumans() }}
                    </small>
                @endif
            </div>

            @if($dok && $dok->file_path)
                {{-- Preview file --}}
                <div class="mb-3">
                    @if($isUrl)
                        <a href="{{ $dok->file_path }}" target="_blank" class="file-link">
                            <i class="bi bi-box-arrow-up-right"></i>
                            {{ \Illuminate\Support\Str::limit($dok->file_path, 60) }}
                        </a>
                    @else
                        @php
                            $ext = strtolower(pathinfo($dok->file_path, PATHINFO_EXTENSION));
                            $assetUrl = asset('storage/' . $dok->file_path);
                        @endphp
                        @if(in_array($ext, ['jpg','jpeg','png','webp']))
                            <a href="{{ $assetUrl }}" target="_blank">
                                <img src="{{ $assetUrl }}" alt="{{ $label }}" class="file-preview">
                            </a>
                        @else
                            <a href="{{ $assetUrl }}" target="_blank" class="file-link">
                                <i class="bi bi-file-earmark-{{ $ext === 'pdf' ? 'pdf' : 'text' }}"></i>
                                Lihat dokumen ({{ strtoupper($ext) }})
                            </a>
                        @endif
                    @endif
                </div>

                @if($dok->catatan)
                    <div class="alert alert-light border rounded-3 py-2 px-3 mb-3" style="font-size:.8rem;">
                        <strong>Catatan sebelumnya:</strong> {{ $dok->catatan }}
                    </div>
                @endif

                {{-- Action buttons --}}
                @if($status !== \App\Models\MitraVerifikasi::STATUS_APPROVED)
                    <form action="{{ route('admin.verification.actionDokumen', $dok->id) }}" method="POST"
                          class="d-flex gap-2 align-items-end flex-wrap">
                        @csrf
                        <div class="flex-grow-1" style="min-width:200px;">
                            <label class="form-label small text-muted mb-1">Catatan (opsional, wajib jika tolak)</label>
                            <input type="text" name="catatan" class="form-control form-control-sm rounded-3"
                                   placeholder="contoh: KTP buram, mohon unggah ulang">
                        </div>
                        <button type="submit" name="action" value="approve"
                                class="btn btn-success btn-sm rounded-pill px-3 fw-bold"
                                onclick="return confirm('Setujui dokumen {{ $label }}?')">
                            <i class="bi bi-check-lg"></i> Setujui
                        </button>
                        <button type="submit" name="action" value="tolak"
                                class="btn btn-danger btn-sm rounded-pill px-3 fw-bold"
                                onclick="return confirm('Tolak dokumen {{ $label }}? Pastikan catatan diisi agar mitra tahu kenapa.')">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                    </form>
                @else
                    {{-- Tombol revoke approval --}}
                    <form action="{{ route('admin.verification.actionDokumen', $dok->id) }}" method="POST"
                          onsubmit="return confirm('Tarik kembali approval? Mitra harus upload ulang.')">
                        @csrf
                        <input type="hidden" name="action" value="tolak">
                        <input type="hidden" name="catatan" value="Approval ditarik oleh admin">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-counterclockwise"></i> Tarik Approval
                        </button>
                    </form>
                @endif
            @else
                <div class="text-muted small fst-italic">
                    Mitra belum mengunggah dokumen ini.
                </div>
            @endif
        </div>
    @endforeach

    {{-- Status keseluruhan --}}
    @php
        $totalWajib = $requiredKeys->where('wajib', true)->count();
        $approvedWajib = $requiredKeys->where('wajib', true)
            ->filter(fn($r) => ($uploaded->get($r->verifikasi_key)?->status) === \App\Models\MitraVerifikasi::STATUS_APPROVED)
            ->count();
    @endphp
    @if($totalWajib > 0)
        <div class="alert {{ $approvedWajib === $totalWajib ? 'alert-success' : 'alert-info' }} rounded-3 mt-3">
            <i class="bi bi-info-circle-fill me-1"></i>
            Progress: <strong>{{ $approvedWajib }} / {{ $totalWajib }}</strong> dokumen wajib disetujui.
            @if($approvedWajib === $totalWajib)
                Mitra <strong>otomatis diaktifkan</strong> karena semua syarat wajib sudah terpenuhi.
            @endif
        </div>
    @endif
@endif
@endsection
