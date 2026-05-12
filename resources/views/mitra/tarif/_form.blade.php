@php
    $isEdit = isset($tarif);
    $tarifNominal = old('nominal', $isEdit ? $tarif->nominal : '');
    $tarifKeterangan = old('keterangan', $isEdit ? $tarif->keterangan : '');
    $tarifSatuan = old('satuan', $isEdit ? $tarif->satuan : 'per item');
    $tarifAktif = $isEdit ? old('is_aktif', $tarif->is_aktif) : old('is_aktif', true);
@endphp

@if($errors->any())
    <div class="alert alert-danger rounded-3">
        @foreach($errors->all() as $err)
            <div><i class="bi bi-x-circle-fill me-1"></i>{{ $err }}</div>
        @endforeach
    </div>
@endif

<div class="mb-3">
    <label class="form-label fw-bold small">Nama Layanan <span class="text-danger">*</span></label>
    <input type="text" name="keterangan"
           class="form-control rounded-3"
           value="{{ $tarifKeterangan }}"
           placeholder="contoh: Service AC, Bersihkan Rumah, Antar Paket"
           required maxlength="255">
</div>

<div class="row g-2 mb-3">
    <div class="col-7">
        <label class="form-label fw-bold small">Tarif Anda <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">Rp</span>
            <input type="number" name="nominal" id="input-nominal"
                   class="form-control"
                   value="{{ $tarifNominal }}"
                   min="1000" max="99999999" step="1000"
                   placeholder="50000" required>
        </div>
        <div class="form-text" style="font-size: .7rem;">Jumlah yang Anda terima</div>
    </div>
    <div class="col-5">
        <label class="form-label fw-bold small">Satuan <span class="text-danger">*</span></label>
        <select name="satuan" class="form-select" required>
            @php $satuanOptions = ['per item','per jam','per hari','per kunjungan','per km','per kg']; @endphp
            @foreach($satuanOptions as $opt)
                <option value="{{ $opt }}" {{ $tarifSatuan === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
</div>

{{-- Preview harga pelanggan --}}
<div class="card border-0 rounded-3 mb-3" style="background: #f8fafc;">
    <div class="card-body p-3">
        <div class="text-muted small mb-1">Pelanggan akan bayar:</div>
        <div class="d-flex justify-content-between align-items-end">
            <div>
                <span class="fw-bold" style="color: #005aa9; font-size: 1.2rem;" id="preview-harga">
                    Rp <span id="preview-harga-nominal">0</span>
                </span>
            </div>
            <small class="text-muted">
                = tarif Anda + {{ number_format($komisiPersen, 1) }}% komisi
            </small>
        </div>
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" name="is_aktif" value="1" id="switchAktif"
           {{ $tarifAktif ? 'checked' : '' }}>
    <label class="form-check-label fw-bold" for="switchAktif">
        Tampilkan tarif ini untuk pelanggan
    </label>
</div>

<div class="d-grid gap-2">
    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
        <i class="bi bi-check-circle me-1"></i>
        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Tarif' }}
    </button>
    <a href="{{ route('mitra.tarif.index') }}" class="btn btn-link text-muted">Batal</a>
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
