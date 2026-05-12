@php
    $isEdit = isset($portfolio);
    $judul = old('judul', $isEdit ? $portfolio->judul : '');
    $deskripsi = old('deskripsi', $isEdit ? $portfolio->deskripsi : '');
    $kategori = old('kategori', $isEdit ? $portfolio->kategori : '');
    $linkUrl = old('link_url', $isEdit ? $portfolio->link_url : '');
    $isFeatured = $isEdit ? old('is_featured', $portfolio->is_featured) : old('is_featured', false);
@endphp

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $err)
            <div><i class="bi bi-x-circle-fill me-1"></i>{{ $err }}</div>
        @endforeach
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger rounded-3">{{ session('error') }}</div>
@endif

<div class="mb-3">
    <label class="form-label fw-bold small">Judul Karya <span class="text-danger">*</span></label>
    <input type="text" name="judul" class="form-control rounded-3"
           value="{{ $judul }}" maxlength="200" required
           placeholder="contoh: Desain Logo Coffee Shop">
</div>

<div class="mb-3">
    <label class="form-label fw-bold small">Kategori</label>
    <input type="text" name="kategori" class="form-control rounded-3"
           value="{{ $kategori }}" maxlength="100"
           placeholder="Desain UI / Coding / Voice Over / dll">
</div>

<div class="mb-3">
    <label class="form-label fw-bold small">Deskripsi</label>
    <textarea name="deskripsi" class="form-control rounded-3"
              rows="3" maxlength="2000"
              placeholder="Ceritakan singkat tentang karya ini...">{{ $deskripsi }}</textarea>
</div>

@if($isEdit && $portfolio->file_path)
    <div class="mb-3 p-2 rounded-3" style="background:#f8fafc;">
        <small class="text-muted">File saat ini:</small>
        @if($portfolio->isImage())
            <img src="{{ asset('storage/' . $portfolio->file_path) }}"
                 style="max-height:120px;border-radius:8px;display:block;margin-top:6px;">
        @else
            <div class="mt-1">
                <a href="{{ asset('storage/' . $portfolio->file_path) }}" target="_blank" class="small">
                    <i class="bi bi-file-earmark"></i> Lihat file
                </a>
            </div>
        @endif
    </div>
@endif

<div class="mb-3">
    <label class="form-label fw-bold small">
        Upload File (JPG/PNG/PDF)
        @if($isEdit)
            <small class="text-muted fw-normal">— kosongkan bila tidak ganti</small>
        @endif
    </label>
    <input type="file" name="file" class="form-control rounded-3"
           accept="image/jpeg,image/png,image/webp,image/gif,application/pdf">
    <small class="text-muted" style="font-size:.7rem;">Max 10 MB</small>
</div>

<div class="mb-3">
    <label class="form-label fw-bold small">Atau Link External</label>
    <input type="url" name="link_url" class="form-control rounded-3"
           value="{{ $linkUrl }}" maxlength="500"
           placeholder="https://behance.net/projek-saya">
    <small class="text-muted" style="font-size:.7rem;">Github, Behance, Dribbble, dll</small>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" name="is_featured" value="1"
           id="switchFeatured" {{ $isFeatured ? 'checked' : '' }}>
    <label class="form-check-label fw-bold" for="switchFeatured">
        <i class="bi bi-star-fill text-warning me-1"></i>Tampilkan sebagai Featured
    </label>
    <div class="form-text" style="font-size:.7rem;">
        Karya featured ditampilkan terlebih dulu di halaman detail mitra
    </div>
</div>

<div class="d-grid gap-2">
    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
        <i class="bi bi-check-circle me-1"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Karya' }}
    </button>
    <a href="{{ route('mitra.portfolio.index') }}" class="btn btn-link text-muted">Batal</a>
</div>
