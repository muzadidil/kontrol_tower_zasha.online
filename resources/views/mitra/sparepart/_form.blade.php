@php
    $isEdit = isset($sparepart);
    $nama        = old('nama',        $isEdit ? $sparepart->nama : '');
    $kode        = old('kode',        $isEdit ? $sparepart->kode : '');
    $kategori    = old('kategori',    $isEdit ? $sparepart->kategori : '');
    $deskripsi   = old('deskripsi',   $isEdit ? $sparepart->deskripsi : '');
    $harga       = old('harga',       $isEdit ? $sparepart->harga : '');
    $hargaModal  = old('harga_modal', $isEdit ? $sparepart->harga_modal : '');
    $stok        = old('stok',        $isEdit ? $sparepart->stok : 0);
    $stokMin     = old('stok_min',    $isEdit ? $sparepart->stok_min : 1);
    $satuan      = old('satuan',      $isEdit ? $sparepart->satuan : 'pcs');
    $isAktif     = $isEdit ? old('is_aktif', $sparepart->is_aktif) : old('is_aktif', true);
@endphp

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $err)
            <div><i class="bi bi-x-circle-fill me-1"></i>{{ $err }}</div>
        @endforeach
    </div>
@endif

<div class="row g-2 mb-2">
    <div class="col-8">
        <label class="form-label fw-bold small">Nama <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control rounded-3"
               value="{{ $nama }}" maxlength="200" required
               placeholder="contoh: Kompresor AC 1 PK">
    </div>
    <div class="col-4">
        <label class="form-label fw-bold small">Kode/SKU</label>
        <input type="text" name="kode" class="form-control rounded-3"
               value="{{ $kode }}" maxlength="100" placeholder="AC-001">
    </div>
</div>

<div class="mb-2">
    <label class="form-label fw-bold small">Kategori</label>
    <input type="text" name="kategori" class="form-control rounded-3"
           value="{{ $kategori }}" maxlength="100"
           placeholder="Kompresor / Freon / Kabel / dll">
</div>

<div class="mb-3">
    <label class="form-label fw-bold small">Deskripsi</label>
    <textarea name="deskripsi" class="form-control rounded-3"
              rows="2" maxlength="1000">{{ $deskripsi }}</textarea>
</div>

<div class="row g-2 mb-2">
    <div class="col-6">
        <label class="form-label fw-bold small">Harga Jual <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" name="harga" class="form-control"
                   value="{{ $harga }}" min="0" max="99999999" step="100" required>
        </div>
    </div>
    <div class="col-6">
        <label class="form-label fw-bold small">Harga Modal</label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" name="harga_modal" class="form-control"
                   value="{{ $hargaModal }}" min="0" max="99999999" step="100">
        </div>
        <small class="text-muted" style="font-size:.7rem;">Untuk lacak margin (opsional)</small>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-4">
        <label class="form-label fw-bold small">Stok <span class="text-danger">*</span></label>
        <input type="number" name="stok" class="form-control rounded-3"
               value="{{ $stok }}" min="0" max="99999" required>
    </div>
    <div class="col-4">
        <label class="form-label fw-bold small">Stok Min</label>
        <input type="number" name="stok_min" class="form-control rounded-3"
               value="{{ $stokMin }}" min="0" max="99999">
        <small class="text-muted" style="font-size:.65rem;">Notif "menipis"</small>
    </div>
    <div class="col-4">
        <label class="form-label fw-bold small">Satuan <span class="text-danger">*</span></label>
        <select name="satuan" class="form-select rounded-3" required>
            @foreach(['pcs','set','box','kg','meter','liter','botol','tabung'] as $opt)
                <option value="{{ $opt }}" {{ $satuan === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
</div>

@if($isEdit && $sparepart->foto_path)
    <div class="mb-2 p-2 rounded-3" style="background:#f8fafc;">
        <small class="text-muted">Foto saat ini:</small>
        <img src="{{ asset('storage/' . $sparepart->foto_path) }}"
             style="max-height:80px;border-radius:8px;display:block;margin-top:6px;">
    </div>
@endif

<div class="mb-3">
    <label class="form-label fw-bold small">
        Foto Sparepart
        @if($isEdit)<small class="text-muted fw-normal">— kosongkan bila tidak ganti</small>@endif
    </label>
    <input type="file" name="foto" class="form-control rounded-3"
           accept="image/jpeg,image/png,image/webp">
    <small class="text-muted" style="font-size:.7rem;">JPG/PNG/WEBP, max 5 MB</small>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" name="is_aktif" value="1"
           id="switchAktif" {{ $isAktif ? 'checked' : '' }}>
    <label class="form-check-label fw-bold" for="switchAktif">Aktif</label>
</div>

<div class="d-grid gap-2">
    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
        <i class="bi bi-check-circle me-1"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Sparepart' }}
    </button>
    @if($isEdit)
        <form action="{{ route('mitra.sparepart.destroy', $sparepart->id) }}" method="POST"
              onsubmit="return confirm('Hapus sparepart &quot;{{ $sparepart->nama }}&quot; secara permanen?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill py-2">
                <i class="bi bi-trash me-1"></i>Hapus
            </button>
        </form>
    @endif
    <a href="{{ route('mitra.sparepart.index') }}" class="btn btn-link text-muted">Batal</a>
</div>
