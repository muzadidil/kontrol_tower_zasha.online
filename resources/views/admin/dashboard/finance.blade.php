@extends('layouts.admin')

@section('content')
<style>
    .stat-card { border: none; border-radius: 16px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 24px; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; border-radius: 50%; opacity: 0.08; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
    .stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 4px; }
    .stat-value { font-size: 1.35rem; font-weight: 800; color: #1e293b; line-height: 1.2; }
    .stat-sub { font-size: 0.72rem; color: #94a3b8; margin-top: 4px; }
    .section-title { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 16px; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size: 0.82rem; color: #64748b; }
    .info-value { font-size: 0.9rem; font-weight: 700; color: #1e293b; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-chart-pie me-2" style="color:#005aa9;"></i>Dashboard Keuangan
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Ringkasan keuangan seluruh platform Zasha.</p>
    </div>
    <span class="badge rounded-pill px-3 py-2" style="background:#eff6ff; color:#005aa9; font-size:0.78rem;">
        <i class="fas fa-sync-alt me-1"></i>Live Data
    </span>
</div>

{{-- STAT CARDS ROW 1 --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl">
        <div class="stat-card">
            <div class="d-flex align-items-start gap-3">
                <div class="stat-icon" style="background:#eff6ff;">
                    <i class="fas fa-receipt" style="color:#005aa9;"></i>
                </div>
                <div>
                    <div class="stat-label">Total Omzet Jasa</div>
                    <div class="stat-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                    <div class="stat-sub">Dari pesanan selesai</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl">
        <div class="stat-card">
            <div class="d-flex align-items-start gap-3">
                <div class="stat-icon" style="background:#f0fdf4;">
                    <i class="fas fa-coins" style="color:#16a34a;"></i>
                </div>
                <div>
                    <div class="stat-label">Cuan Zasha (Jastip)</div>
                    <div class="stat-value">Rp {{ number_format($totalCuanZasha, 0, ',', '.') }}</div>
                    <div class="stat-sub">Komisi platform</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl">
        <div class="stat-card">
            <div class="d-flex align-items-start gap-3">
                <div class="stat-icon" style="background:#faf5ff;">
                    <i class="fas fa-bolt" style="color:#7c3aed;"></i>
                </div>
                <div>
                    <div class="stat-label">Profit PPOB</div>
                    <div class="stat-value">Rp {{ number_format($totalProfitPpob ?? 0, 0, ',', '.') }}</div>
                    <div class="stat-sub">Selisih harga jual</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl">
        <div class="stat-card">
            <div class="d-flex align-items-start gap-3">
                <div class="stat-icon" style="background:#fff7ed;">
                    <i class="fas fa-lock" style="color:#ea580c;"></i>
                </div>
                <div>
                    <div class="stat-label">Dana Escrow</div>
                    <div class="stat-value">Rp {{ number_format($totalDanaEscrow, 0, ',', '.') }}</div>
                    <div class="stat-sub">Pesanan belum selesai</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl">
        <div class="stat-card">
            <div class="d-flex align-items-start gap-3">
                <div class="stat-icon" style="background:#fef2f2;">
                    <i class="fas fa-piggy-bank" style="color:#dc2626;"></i>
                </div>
                <div>
                    <div class="stat-label">Saldo Mengendap</div>
                    <div class="stat-value">Rp {{ number_format($totalSaldoMengendap, 0, ',', '.') }}</div>
                    <div class="stat-sub">Mitra + Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SUMMARY BREAKDOWN --}}
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 p-4" style="background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div class="section-title">Ringkasan Pendapatan</div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-circle me-2" style="color:#005aa9; font-size:0.5rem;"></i>Omzet Jasa (Pesanan Selesai)</span>
                <span class="info-value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-circle me-2" style="color:#16a34a; font-size:0.5rem;"></i>Komisi Jastip Zasha</span>
                <span class="info-value">Rp {{ number_format($totalCuanZasha, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-circle me-2" style="color:#7c3aed; font-size:0.5rem;"></i>Profit PPOB</span>
                <span class="info-value">Rp {{ number_format($totalProfitPpob ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="info-row" style="border-bottom:none; margin-top:8px; padding-top:14px; border-top:2px solid #f1f5f9;">
                <span class="info-label fw-bold" style="color:#1e293b;">Total Estimasi Pendapatan</span>
                <span class="info-value" style="color:#005aa9; font-size:1rem;">Rp {{ number_format($totalOmzet + $totalCuanZasha + ($totalProfitPpob ?? 0), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 p-4" style="background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div class="section-title">Status Dana Platform</div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-hourglass-half me-2 text-warning"></i>Dana Tertahan (Escrow)</span>
                <span class="info-value">Rp {{ number_format($totalDanaEscrow, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label"><i class="fas fa-wallet me-2" style="color:#dc2626;"></i>Saldo Aktif Pengguna</span>
                <span class="info-value">Rp {{ number_format($totalSaldoMengendap, 0, ',', '.') }}</span>
            </div>
            <div class="info-row" style="border-bottom:none; margin-top:8px; padding-top:14px; border-top:2px solid #f1f5f9;">
                <span class="info-label fw-bold" style="color:#1e293b;">Total Kewajiban Platform</span>
                <span class="info-value" style="color:#dc2626; font-size:1rem;">Rp {{ number_format($totalDanaEscrow + $totalSaldoMengendap, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- PENGATURAN SISTEM --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card border-0 rounded-4 p-4" style="background:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.06);">
            <div class="section-title d-flex align-items-center gap-2">
                <i class="fas fa-cog" style="color:#005aa9;"></i> Pengaturan Sistem
            </div>

            @if(session('setting_saved'))
                <div class="alert border-0 rounded-3 mb-3 d-flex align-items-center gap-2"
                     style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
                    <i class="fas fa-check-circle"></i> {{ session('setting_saved') }}
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-lg-8">
                        <label class="form-label-sm">Google Maps API Key</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background:#f8fafc; border-color:#e5e7eb;">
                                <i class="fas fa-map-marker-alt" style="color:#005aa9;"></i>
                            </span>
                            <input type="text" name="google_maps_api_key"
                                   class="form-control"
                                   value="{{ $gmaps_api_key }}"
                                   placeholder="AIzaSy..."
                                   style="border-color:#e5e7eb; font-family:monospace; font-size:0.82rem;">
                            <button type="button" id="toggleKey"
                                    class="input-group-text" style="background:#f8fafc; border-color:#e5e7eb; cursor:pointer;">
                                <i class="fas fa-eye" style="font-size:0.8rem; color:#64748b;"></i>
                            </button>
                        </div>
                        <div class="mt-1" style="font-size:0.72rem; color:#94a3b8;">
                            <i class="fas fa-info-circle me-1"></i>
                            Digunakan untuk fitur pilih lokasi alamat pelanggan via Google Maps.
                            Pastikan API sudah aktifkan <strong>Maps JavaScript API</strong> dan <strong>Places API</strong>.
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <button type="submit" class="btn w-100 fw-bold rounded-pill"
                                style="background:#005aa9; color:white; font-size:0.875rem; padding:10px;">
                            <i class="fas fa-save me-2"></i>Simpan API Key
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('toggleKey').addEventListener('click', function() {
    const inp = this.previousElementSibling;
    const ico = this.querySelector('i');
    if (inp.type === 'text') {
        inp.type = 'password';
        ico.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
        inp.type = 'text';
        ico.classList.replace('fa-eye', 'fa-eye-slash');
    }
});
// Default: sembunyikan key jika sudah ada isinya
document.addEventListener('DOMContentLoaded', function() {
    const inp = document.querySelector('input[name=google_maps_api_key]');
    if (inp && inp.value.length > 0) inp.type = 'password';
});
</script>
@endpush
@endsection
