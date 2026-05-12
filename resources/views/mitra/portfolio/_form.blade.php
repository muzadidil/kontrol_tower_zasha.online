@php
    $isEdit = isset($portfolio);
    $judul = old('judul', $isEdit ? $portfolio->judul : '');
    $deskripsi = old('deskripsi', $isEdit ? $portfolio->deskripsi : '');
    $kategori = old('kategori', $isEdit ? $portfolio->kategori : '');
    $linkUrl = old('link_url', $isEdit ? $portfolio->link_url : '');
    $isFeatured = $isEdit ? old('is_featured', $portfolio->is_featured) : old('is_featured', false);
@endphp

@if($errors->any())
    <div class="m-alert m-alert-error" style="flex-direction:column; align-items:flex-start;">
        @foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill"></i>{{ $err }}</div>@endforeach
    </div>
@endif
@if(session('error'))<div class="m-alert m-alert-error"><i class="bi bi-x-circle-fill"></i>{{ session('error') }}</div>@endif

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">Judul Karya <span style="color:#ef4444;">*</span></label>
    <input type="text" name="judul" class="m-form-input" value="{{ $judul }}" maxlength="200" required
           placeholder="contoh: Desain Logo Coffee Shop">
</div>

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">Kategori</label>
    <input type="text" name="kategori" class="m-form-input" value="{{ $kategori }}" maxlength="100"
           placeholder="Desain UI / Coding / Voice Over / dll">
</div>

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">Deskripsi</label>
    <textarea name="deskripsi" class="m-form-textarea" rows="3" maxlength="2000"
              placeholder="Ceritakan singkat tentang karya ini...">{{ $deskripsi }}</textarea>
</div>

@if($isEdit && $portfolio->file_path)
    <div style="padding:var(--fib-2); background:var(--mitra-blue-tint); border-radius:var(--r-md); margin-bottom:var(--fib-3);">
        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">File saat ini:</div>
        @if($portfolio->isImage())
            <img src="{{ asset('storage/' . $portfolio->file_path) }}" style="max-height:var(--fib-7); border-radius:var(--r-sm); display:block; margin-top:var(--fib-1);">
        @else
            <a href="{{ asset('storage/' . $portfolio->file_path) }}" target="_blank" style="font-size:var(--t-xs); color:var(--mitra-blue); text-decoration:none;">
                <i class="bi bi-file-earmark"></i> Lihat file
            </a>
        @endif
    </div>
@endif

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">
        Upload File (JPG/PNG/PDF)
        @if($isEdit)<span style="font-weight:400; color:var(--ink-soft);">— kosongkan bila tidak ganti</span>@endif
    </label>
    <input type="file" name="file" class="m-form-input" accept="image/jpeg,image/png,image/webp,image/gif,application/pdf">
    <div class="m-form-help">Max 10 MB</div>
</div>

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">Atau Link External</label>
    <input type="url" name="link_url" class="m-form-input" value="{{ $linkUrl }}" maxlength="500"
           placeholder="https://behance.net/projek-saya">
    <div class="m-form-help">Github, Behance, Dribbble, dll</div>
</div>

<label style="display:flex; align-items:flex-start; gap:var(--fib-2); cursor:pointer; margin-bottom:var(--fib-4);">
    <input type="checkbox" name="is_featured" value="1" {{ $isFeatured ? 'checked' : '' }}
           style="accent-color:#fbbf24; margin-top:3px;">
    <div>
        <div style="font-weight:700; color:var(--ink); font-size:var(--t-sm);">
            <i class="bi bi-star-fill" style="color:#fbbf24;"></i> Tampilkan sebagai Featured
        </div>
        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Karya featured ditampilkan terlebih dulu di halaman detail mitra</div>
    </div>
</label>

<div style="display:flex; flex-direction:column; gap:var(--fib-2);">
    <button type="submit" class="m-btn-primary m-btn-primary-block">
        <i class="bi bi-check-circle"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Karya' }}
    </button>
    <a href="{{ route('mitra.portfolio.index') }}" style="text-align:center; color:var(--ink-soft); font-size:var(--t-xs); text-decoration:none;">Batal</a>
</div>
