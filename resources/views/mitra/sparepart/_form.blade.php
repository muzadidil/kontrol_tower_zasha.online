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
    <div class="m-alert m-alert-error" style="flex-direction:column; align-items:flex-start;">
        @foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill"></i>{{ $err }}</div>@endforeach
    </div>
@endif

<div style="display:grid; grid-template-columns: 1.618fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-2);">
    <div>
        <label class="m-form-label">Nama <span style="color:#ef4444;">*</span></label>
        <input type="text" name="nama" class="m-form-input" value="{{ $nama }}" maxlength="200" required
               placeholder="contoh: Kompresor AC 1 PK">
    </div>
    <div>
        <label class="m-form-label">Kode/SKU</label>
        <input type="text" name="kode" class="m-form-input" value="{{ $kode }}" maxlength="100" placeholder="AC-001">
    </div>
</div>

<div style="margin-bottom:var(--fib-2);">
    <label class="m-form-label">Kategori</label>
    <input type="text" name="kategori" class="m-form-input" value="{{ $kategori }}" maxlength="100"
           placeholder="Kompresor / Freon / Kabel / dll">
</div>

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">Deskripsi</label>
    <textarea name="deskripsi" class="m-form-textarea" rows="2" maxlength="1000">{{ $deskripsi }}</textarea>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-2);">
    <div>
        <label class="m-form-label">Harga Jual <span style="color:#ef4444;">*</span></label>
        <div style="display:flex; align-items:stretch; border:1px solid var(--line); border-radius:var(--r-md); overflow:hidden;">
            <span style="padding:var(--fib-2) var(--fib-3); background:var(--mitra-blue-tint); color:var(--ink-soft); font-size:var(--t-sm);">Rp</span>
            <input type="number" name="harga" class="m-form-input" style="border:none; border-radius:0;"
                   value="{{ $harga }}" min="0" max="99999999" step="100" required>
        </div>
    </div>
    <div>
        <label class="m-form-label">Harga Modal</label>
        <div style="display:flex; align-items:stretch; border:1px solid var(--line); border-radius:var(--r-md); overflow:hidden;">
            <span style="padding:var(--fib-2) var(--fib-3); background:var(--mitra-blue-tint); color:var(--ink-soft); font-size:var(--t-sm);">Rp</span>
            <input type="number" name="harga_modal" class="m-form-input" style="border:none; border-radius:0;"
                   value="{{ $hargaModal }}" min="0" max="99999999" step="100">
        </div>
        <div class="m-form-help">Untuk lacak margin (opsional)</div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-2);">
    <div>
        <label class="m-form-label">Stok <span style="color:#ef4444;">*</span></label>
        <input type="number" name="stok" class="m-form-input" value="{{ $stok }}" min="0" max="99999" required>
    </div>
    <div>
        <label class="m-form-label">Stok Min</label>
        <input type="number" name="stok_min" class="m-form-input" value="{{ $stokMin }}" min="0" max="99999">
        <div class="m-form-help">Notif "menipis"</div>
    </div>
    <div>
        <label class="m-form-label">Satuan <span style="color:#ef4444;">*</span></label>
        <select name="satuan" class="m-form-select" required>
            @foreach(['pcs','set','box','kg','meter','liter','botol','tabung'] as $opt)
                <option value="{{ $opt }}" {{ $satuan === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
</div>

@if($isEdit && $sparepart->foto_path)
    <div style="padding:var(--fib-2); background:var(--mitra-blue-tint); border-radius:var(--r-md); margin-bottom:var(--fib-2);">
        <div style="font-size:var(--t-xxs); color:var(--ink-soft);">Foto saat ini:</div>
        <img src="{{ asset('storage/' . $sparepart->foto_path) }}" style="max-height:var(--fib-7); border-radius:var(--r-sm); display:block; margin-top:var(--fib-1);">
    </div>
@endif

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">
        Foto Sparepart
        @if($isEdit)<span style="font-weight:400; color:var(--ink-soft);">— kosongkan bila tidak ganti</span>@endif
    </label>
    <input type="file" name="foto" class="m-form-input" accept="image/jpeg,image/png,image/webp">
    <div class="m-form-help">JPG/PNG/WEBP, max 5 MB</div>
</div>

<label style="display:flex; align-items:center; gap:var(--fib-2); cursor:pointer; margin-bottom:var(--fib-4); font-weight:700; color:var(--ink); font-size:var(--t-sm);">
    <input type="checkbox" name="is_aktif" value="1" {{ $isAktif ? 'checked' : '' }} style="accent-color:var(--mitra-blue);">
    Aktif
</label>

<div style="display:flex; flex-direction:column; gap:var(--fib-2);">
    <button type="submit" class="m-btn-primary m-btn-primary-block">
        <i class="bi bi-check-circle"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Sparepart' }}
    </button>
    @if($isEdit)
        <form action="{{ route('mitra.sparepart.destroy', $sparepart->id) }}" method="POST"
              onsubmit="return confirm('Hapus sparepart &quot;{{ $sparepart->nama }}&quot; secara permanen?')">
            @csrf @method('DELETE')
            <button type="submit" class="m-btn-primary m-btn-primary-block" style="background:#fff; color:#ef4444; border:1px solid #ef4444;">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>
    @endif
    <a href="{{ route('mitra.sparepart.index') }}" style="text-align:center; color:var(--ink-soft); font-size:var(--t-xs); text-decoration:none;">Batal</a>
</div>
