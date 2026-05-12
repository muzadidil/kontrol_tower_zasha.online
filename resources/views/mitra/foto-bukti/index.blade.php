@extends('layouts.mitra')

@section('content')
@php
    $totalFoto = collect($foto)->sum(fn($g) => $g->count());
@endphp

{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="javascript:history.back()" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">FOTO BUKTI</div>
            <h5 class="fw-bold m-0">Order #{{ $tracking->id }}</h5>
        </div>
    </div>
    <div class="text-center" style="font-size:.75rem;opacity:.85;">
        {{ ucfirst($tracking->order_type) }} · {{ $totalFoto }} foto · Status: {{ $tracking->status }}
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
                <i class="bi bi-cloud-upload me-1" style="color:#005aa9;"></i>Upload Foto Bukti
            </h6>

            <form action="{{ route('mitra.foto-bukti.upload', $tracking->id) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">Tipe Foto <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        @foreach(\App\Models\OrderFotoBukti::TIPE_LABEL as $key => $label)
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="tipe" id="tipe-{{ $key }}"
                                       value="{{ $key }}" autocomplete="off"
                                       {{ old('tipe', 'sebelum') === $key ? 'checked' : '' }} required>
                                <label class="btn btn-outline-primary rounded-3 w-100 py-2"
                                       for="tipe-{{ $key }}" style="font-size:.75rem;">
                                    @if($key === 'sebelum')<i class="bi bi-arrow-up-circle"></i>
                                    @elseif($key === 'proses')<i class="bi bi-arrow-repeat"></i>
                                    @else<i class="bi bi-check-circle"></i>
                                    @endif
                                    <div style="font-size:.65rem;">{{ $label }}</div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">Foto <span class="text-danger">*</span></label>
                    <input type="file" name="foto[]" class="form-control rounded-3"
                           accept="image/jpeg,image/png,image/webp"
                           capture="environment"
                           multiple required>
                    <small class="text-muted" style="font-size:.7rem;">
                        Bisa pilih beberapa foto · max 10 file · JPG/PNG/WEBP max 8MB
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold mb-1">Catatan</label>
                    <textarea name="catatan" class="form-control rounded-3" rows="2"
                              maxlength="500"
                              placeholder="contoh: AC sudah dibersihkan, tinggal pasang covernya"></textarea>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold">
                    <i class="bi bi-cloud-upload me-1"></i>Upload Foto
                </button>
            </form>
        </div>
    </div>

    {{-- Galeri per Tipe --}}
    @foreach(\App\Models\OrderFotoBukti::TIPE_LABEL as $tipe => $label)
        @php $fotoTipe = $foto->get($tipe) ?? collect(); @endphp
        @if($fotoTipe->isNotEmpty())
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold m-0" style="color:#1e293b;font-size:.85rem;">
                            @if($tipe === 'sebelum')<i class="bi bi-arrow-up-circle me-1" style="color:#005aa9;"></i>
                            @elseif($tipe === 'proses')<i class="bi bi-arrow-repeat me-1" style="color:#f59e0b;"></i>
                            @else<i class="bi bi-check-circle me-1" style="color:#16a34a;"></i>
                            @endif
                            {{ $label }}
                        </h6>
                        <small class="text-muted">{{ $fotoTipe->count() }} foto</small>
                    </div>

                    <div class="row g-2">
                        @foreach($fotoTipe as $f)
                            <div class="col-4">
                                <div class="position-relative">
                                    <a href="{{ asset('storage/' . $f->file_path) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $f->file_path) }}"
                                             style="width:100%;height:100px;object-fit:cover;
                                                    border-radius:8px;display:block;">
                                    </a>
                                    <form action="{{ route('mitra.foto-bukti.destroy', $f->id) }}" method="POST"
                                          class="position-absolute" style="top:4px;right:4px;"
                                          onsubmit="return confirm('Hapus foto ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-danger rounded-circle p-0"
                                                style="width:24px;height:24px;font-size:.7rem;
                                                       background:rgba(239,68,68,.85);border:none;">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                </div>
                                @if($f->catatan)
                                    <small class="text-muted d-block text-truncate mt-1" style="font-size:.6rem;"
                                           title="{{ $f->catatan }}">
                                        {{ $f->catatan }}
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if($totalFoto === 0)
        <div class="card border-0 shadow-sm rounded-4 text-center py-4">
            <i class="bi bi-camera" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-2 fw-bold">Belum Ada Foto Bukti</h6>
            <p class="text-muted small mb-0">
                Upload foto sebelum/proses/sesudah pekerjaan untuk lindungi diri Anda dari dispute.
            </p>
        </div>
    @endif

    <div class="alert alert-info rounded-3 small mt-3 mb-0"
         style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;font-size:.75rem;">
        <i class="bi bi-shield-fill-check me-1"></i>
        <strong>Tips:</strong> Foto bukti membantu jika pelanggan komplain. Foto "sebelum" dan "sesudah"
        adalah penting untuk membuktikan kerja Anda.
    </div>
</div>
@endsection
