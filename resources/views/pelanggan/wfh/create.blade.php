@extends('layouts.pelanggan')
@section('title', 'Order WFH — ' . $layanan->masterLayanan->nama_layanan)

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <a href="{{ url()->previous() }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="bg-warning bg-opacity-15 rounded-3 p-3">
                <i class="fas fa-laptop-code fa-2x text-warning"></i>
            </div>
            <div>
                <div class="fw-bold">{{ $layanan->masterLayanan->nama_layanan }}</div>
                <div class="text-muted small">{{ $layanan->mitra->nama_asli ?? $layanan->mitra->nama_panggilan }}</div>
                <div class="text-warning fw-semibold small">
                    @if($layanan->tarif_per_project)
                        Per Project: Rp {{ number_format($layanan->tarif_per_project, 0, ',', '.') }}
                    @elseif($layanan->tarif_per_unit)
                        Per {{ $layanan->satuan_unit ?? 'Unit' }}: Rp {{ number_format($layanan->tarif_per_unit, 0, ',', '.') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4">Detail Order</h5>

            <form action="{{ route('pelanggan.wfh.store') }}" method="POST">
                @csrf
                <input type="hidden" name="mitra_layanan_id" value="{{ $layanan->id }}">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                    <textarea name="brief_description" class="form-control @error('brief_description') is-invalid @enderror"
                        rows="5" placeholder="Jelaskan detail pekerjaan yang Anda inginkan, termasuk format, gaya, referensi, dll."
                        required>{{ old('brief_description') }}</textarea>
                    @error('brief_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">Minimal 20 karakter. Semakin detail semakin baik.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Link Referensi <span class="text-muted small">(opsional)</span></label>
                    <input type="url" name="reference_url" class="form-control @error('reference_url') is-invalid @enderror"
                        placeholder="https://drive.google.com/..." value="{{ old('reference_url') }}">
                    @error('reference_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if($layanan->tarif_per_unit)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Jumlah ({{ $layanan->satuan_unit ?? 'unit' }}) <span class="text-danger">*</span></label>
                    <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                        min="0.5" step="0.5" value="{{ old('quantity', 1) }}" required>
                    @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @else
                    <input type="hidden" name="quantity" value="1">
                @endif

                <div class="mb-4">
                    <label class="form-label fw-semibold">Tipe Order <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="tipe_order" id="reguler" value="reguler" checked>
                            <label class="btn btn-outline-secondary w-100 text-start p-3" for="reguler">
                                <div class="fw-semibold">Reguler</div>
                                <div class="small text-muted">Deadline 3 hari</div>
                            </label>
                        </div>
                        <div class="col-6">
                            <input type="radio" class="btn-check" name="tipe_order" id="express" value="express">
                            <label class="btn btn-outline-warning w-100 text-start p-3" for="express">
                                <div class="fw-semibold">Express ⚡</div>
                                <div class="small text-muted">Deadline 1 hari ({{ $layanan->express_multiplier ?? 1.5 }}x tarif)</div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning border-warning d-flex align-items-center gap-2 mb-4">
                    <i class="fas fa-shield-alt text-warning"></i>
                    <div class="small">Dana Anda akan ditahan dalam sistem escrow dan baru diteruskan ke mitra setelah Anda konfirmasi pekerjaan selesai.</div>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold py-2">
                    <i class="fas fa-lock me-2"></i> Buat Order & Bayar via Saldo
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
