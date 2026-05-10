@extends('layouts.admin')

@section('content')
<style>
    .setting-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 28px; margin-bottom: 20px; }
    .setting-section-title { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 18px; display: flex; align-items: center; gap: 6px; }
    .setting-label { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    .setting-hint { font-size: 0.72rem; color: #94a3b8; margin-top: 6px; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-cog me-2" style="color:#005aa9;"></i>Pengaturan Sistem
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Konfigurasi API dan integrasi layanan eksternal.</p>
    </div>
</div>

@if(session('setting_saved'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
        <i class="fas fa-check-circle"></i> {{ session('setting_saved') }}
    </div>
@endif

{{-- Google Maps --}}
<div class="setting-card">
    <div class="setting-section-title">
        <i class="fas fa-map-marked-alt" style="color:#005aa9;"></i> Integrasi Google Maps
    </div>
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-lg-9">
                <label class="setting-label">Google Maps API Key</label>
                <div class="input-group">
                    <span class="input-group-text" style="background:#f8fafc; border-color:#e2e8f0;">
                        <i class="fas fa-key" style="color:#005aa9; font-size:0.85rem;"></i>
                    </span>
                    <input type="text" name="google_maps_api_key" id="mapsApiKey"
                           class="form-control"
                           value="{{ $gmaps_api_key }}"
                           placeholder="AIzaSy..."
                           style="border-color:#e2e8f0; font-family:monospace; font-size:0.83rem; border-left:none;">
                    <button type="button" id="toggleKey"
                            class="input-group-text"
                            style="background:#f8fafc; border-color:#e2e8f0; cursor:pointer;">
                        <i class="fas fa-eye" style="font-size:0.8rem; color:#64748b;"></i>
                    </button>
                </div>
                <div class="setting-hint">
                    <i class="fas fa-info-circle me-1"></i>
                    Digunakan untuk fitur pilih lokasi alamat pelanggan via peta interaktif.
                    Pastikan sudah aktifkan <strong>Maps JavaScript API</strong> dan <strong>Places API</strong> di Google Cloud Console.
                </div>
            </div>
            <div class="col-lg-3">
                <button type="submit" class="btn w-100 fw-bold rounded-3"
                        style="background:#005aa9; color:white; font-size:0.875rem; padding:10px 0;">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </div>

        {{-- Status indicator --}}
        <div class="mt-3 d-flex align-items-center gap-2" style="font-size:0.78rem;">
            @if($gmaps_api_key)
                <span class="badge rounded-pill" style="background:#dcfce7; color:#15803d; padding:4px 10px;">
                    <i class="fas fa-check-circle me-1"></i>API Key tersimpan
                </span>
                <span style="color:#94a3b8;">Fitur peta aktif untuk pelanggan.</span>
            @else
                <span class="badge rounded-pill" style="background:#fef2f2; color:#dc2626; padding:4px 10px;">
                    <i class="fas fa-times-circle me-1"></i>Belum dikonfigurasi
                </span>
                <span style="color:#94a3b8;">Pelanggan akan input alamat manual tanpa peta.</span>
            @endif
        </div>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('toggleKey').addEventListener('click', function() {
    const inp = document.getElementById('mapsApiKey');
    const ico = this.querySelector('i');
    if (inp.type === 'text') {
        inp.type = 'password';
        ico.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        inp.type = 'text';
        ico.classList.replace('fa-eye', 'fa-eye-slash');
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const inp = document.getElementById('mapsApiKey');
    if (inp && inp.value.length > 0) inp.type = 'password';
});
</script>
@endpush
@endsection
