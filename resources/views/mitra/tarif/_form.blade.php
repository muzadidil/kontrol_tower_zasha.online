@php
    $isEdit = isset($tarif);
    $tarifNominal = old('nominal', $isEdit ? $tarif->nominal : '');
    $tarifKeterangan = old('keterangan', $isEdit ? $tarif->keterangan : '');
    $tarifSatuan = old('satuan', $isEdit ? $tarif->satuan : 'per item');
    $tarifAktif = $isEdit ? old('is_aktif', $tarif->is_aktif) : old('is_aktif', true);
@endphp

@if($errors->any())
    <div class="m-alert m-alert-error" style="flex-direction:column; align-items:flex-start;">
        @foreach($errors->all() as $err)
            <div><i class="bi bi-x-circle-fill"></i>{{ $err }}</div>
        @endforeach
    </div>
@endif

<div style="margin-bottom:var(--fib-3);">
    <label class="m-form-label">Nama Layanan <span style="color:#ef4444;">*</span></label>
    <input type="text" name="keterangan" class="m-form-input"
           value="{{ $tarifKeterangan }}" maxlength="255" required
           placeholder="contoh: Service AC, Bersihkan Rumah, Antar Paket">
</div>

<div style="display:grid; grid-template-columns: 1.618fr 1fr; gap:var(--fib-2); margin-bottom:var(--fib-3);">
    <div>
        <label class="m-form-label">Tarif Anda <span style="color:#ef4444;">*</span></label>
        <div style="display:flex; align-items:stretch; border:1px solid var(--line); border-radius:var(--r-md); overflow:hidden;">
            <span style="padding:var(--fib-2) var(--fib-3); background:var(--mitra-blue-tint); color:var(--ink-soft); font-size:var(--t-sm);">Rp</span>
            <input type="number" name="nominal" id="input-nominal"
                   class="m-form-input" style="border:none; border-radius:0;"
                   value="{{ $tarifNominal }}"
                   min="1000" max="99999999" step="1000"
                   placeholder="50000" required>
        </div>
        <div class="m-form-help">Jumlah yang Anda terima</div>
    </div>
    <div>
        <label class="m-form-label">Satuan <span style="color:#ef4444;">*</span></label>
        <select name="satuan" class="m-form-select" required>
            @php $satuanOptions = ['per item','per jam','per hari','per kunjungan','per km','per kg']; @endphp
            @foreach($satuanOptions as $opt)
                <option value="{{ $opt }}" {{ $tarifSatuan === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Preview harga pelanggan --}}
<div style="background:var(--mitra-blue-tint); border-radius:var(--r-md); padding:var(--fib-3); margin-bottom:var(--fib-3);">
    <div style="font-size:var(--t-xxs); color:var(--ink-soft); margin-bottom:var(--fib-1);">Pelanggan akan bayar:</div>
    <div style="display:flex; justify-content:space-between; align-items:flex-end;">
        <span style="font-weight:800; color:var(--mitra-blue); font-size:var(--t-lg);">
            Rp <span id="preview-harga-nominal">0</span>
        </span>
        <span style="font-size:var(--t-xxs); color:var(--ink-soft);">
            = tarif Anda + {{ number_format($komisiPersen, 1) }}% komisi
        </span>
    </div>
</div>

<div style="display:flex; align-items:center; gap:var(--fib-2); margin-bottom:var(--fib-4);">
    <label style="display:flex; align-items:center; gap:var(--fib-2); cursor:pointer; font-weight:700; color:var(--ink); font-size:var(--t-sm);">
        <input type="checkbox" name="is_aktif" value="1" {{ $tarifAktif ? 'checked' : '' }}
               style="width:var(--fib-3); height:var(--fib-3); accent-color:var(--mitra-blue);">
        Tampilkan tarif ini untuk pelanggan
    </label>
</div>

<div style="display:flex; flex-direction:column; gap:var(--fib-2);">
    <button type="submit" class="m-btn-primary m-btn-primary-block">
        <i class="bi bi-check-circle"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Tarif' }}
    </button>
    <a href="{{ route('mitra.tarif.index') }}" style="text-align:center; color:var(--ink-soft); font-size:var(--t-xs); text-decoration:none;">Batal</a>
</div>

<script>
(function () {
    const komisi = {{ $komisiPersen }};
    const input = document.getElementById('input-nominal');
    const preview = document.getElementById('preview-harga-nominal');

    function updatePreview() {
        const nominal = parseFloat(input.value) || 0;
        const harga = nominal * (1 + komisi / 100);
        preview.textContent = harga.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    }
    input.addEventListener('input', updatePreview);
    updatePreview();
})();
</script>
