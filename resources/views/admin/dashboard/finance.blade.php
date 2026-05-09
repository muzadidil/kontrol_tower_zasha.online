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
@endsection
