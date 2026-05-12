@extends('layouts.mitra')

@section('content')
@php
    $totalFinal = $dokumens->where('is_final', true)->count();
    $totalSize = $dokumens->sum('size_bytes');
    $sizeFormatted = $totalSize < 1024*1024
        ? round($totalSize / 1024, 1) . ' KB'
        : round($totalSize / (1024*1024), 1) . ' MB';
@endphp

{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="javascript:history.back()" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">DOKUMEN KERJA</div>
            <h5 class="fw-bold m-0">Order #{{ $tracking->id }}</h5>
        </div>
    </div>
    <div class="text-center" style="font-size:.75rem;opacity:.85;">
        {{ ucfirst($tracking->order_type) }} · {{ $dokumens->count() }} dokumen
        @if($totalFinal > 0)
            · <span style="background:#fbbf24;color:#fff;padding:2px 8px;border-radius:999px;">
                {{ $totalFinal }} final
            </span>
        @endif
        · {{ $sizeFormatted }}
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-3 small">
            @foreach($errors->all() as $err)
                <div><i class="bi bi-x-circle-fill me-1"></i>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- Form Upload --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b;">
                <i class="bi bi-cloud-upload me-1" style="color:#005aa9;"></i>Upload Dokumen Baru
            </h6>

            <form action="{{ route('mitra.dokumen.upload', $tracking->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control rounded-3"
                           placeholder="contoh: Draft Desain Logo v1"
                           value="{{ old('judul') }}"
                           maxlength="200" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">File <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control rounded-3" required>
                    <small class="text-muted" style="font-size:.7rem;">Max 25 MB · semua tipe file diterima</small>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">Catatan</label>
                    <textarea name="catatan" class="form-control rounded-3" rows="2"
                              maxlength="500"
                              placeholder="contoh: revisi pertama berdasar feedback"></textarea>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="is_final" value="1" id="switchFinal">
                    <label class="form-check-label fw-bold small" for="switchFinal">
                        <i class="bi bi-bookmark-star-fill text-warning me-1"></i>Tandai sebagai versi FINAL
                    </label>
                    <div class="form-text" style="font-size:.7rem;">
                        Dokumen final ditampilkan di paling atas
                    </div>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold">
                    <i class="bi bi-cloud-upload me-1"></i>Upload Dokumen
                </button>
            </form>
        </div>
    </div>

    {{-- List Dokumen --}}
    @if($dokumens->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-folder2-open" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Dokumen</h6>
            <p class="text-muted small mb-0">
                Upload hasil kerja agar pelanggan bisa download dan review.
            </p>
        </div>
    @else
        @foreach($dokumens as $d)
            <div class="card border-0 shadow-sm rounded-4 mb-2 {{ $d->is_final ? 'border-warning' : '' }}"
                 style="{{ $d->is_final ? 'border-left:4px solid #fbbf24!important;' : '' }}">
                <div class="card-body p-3">
                    <div class="d-flex gap-3 align-items-start">
                        <div style="width:48px;height:48px;border-radius:10px;
                                    background:#eff6ff;color:#005aa9;flex-shrink:0;
                                    display:flex;align-items:center;justify-content:center;
                                    font-size:1.5rem;">
                            <i class="bi {{ $d->icon_class }}"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap mb-1">
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-bold small text-truncate" style="color:#1e293b;">
                                        {{ $d->judul }}
                                        @if($d->is_final)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:.6rem;">
                                                <i class="bi bi-bookmark-star-fill"></i> Final
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted" style="font-size:.65rem;">
                                        {{ $d->size_formatted }} · {{ $d->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>

                            @if($d->catatan)
                                <small class="text-muted d-block" style="font-size:.7rem;">
                                    {{ $d->catatan }}
                                </small>
                            @endif

                            <div class="d-flex gap-1 mt-2">
                                <a href="{{ asset('storage/' . $d->file_path) }}" target="_blank"
                                   class="btn btn-sm btn-primary rounded-pill flex-grow-1 py-1"
                                   style="font-size:.7rem;">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <form action="{{ route('mitra.dokumen.toggleFinal', $d->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm rounded-pill px-2 py-1
                                                {{ $d->is_final ? 'btn-warning' : 'btn-outline-warning' }}"
                                            style="font-size:.7rem;">
                                        <i class="bi {{ $d->is_final ? 'bi-bookmark-star-fill' : 'bi-bookmark-star' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('mitra.dokumen.destroy', $d->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus &quot;{{ $d->judul }}&quot;?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1"
                                            style="font-size:.7rem;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="alert alert-info rounded-3 small mt-3 mb-0"
         style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;font-size:.75rem;">
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>Tips:</strong> Tandai dokumen <i class="bi bi-bookmark-star-fill text-warning"></i> <strong>Final</strong>
        agar pelanggan tahu mana versi yang sudah disepakati. Versi draft tetap bisa diupload sebagai referensi.
    </div>
</div>
@endsection
