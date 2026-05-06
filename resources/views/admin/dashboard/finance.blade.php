@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 fw-bold text-dark">Dashboard Keuangan</h2>
    
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card p-4">
                <div class="text-uppercase text-muted fw-bold small mb-2">Total Omzet</div>
                <div class="h4 fw-bold text-dark mb-0">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <div class="text-uppercase text-muted fw-bold small mb-2">Total Cuan Zasha (5%)</div>
                <div class="h4 fw-bold text-dark mb-0">Rp {{ number_format($totalCuanZasha, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <div class="text-uppercase text-muted fw-bold small mb-2">Total Profit PPOB</div>
                <div class="h4 fw-bold text-dark mb-0">Rp {{ number_format($totalProfitPpob, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <div class="text-uppercase text-muted fw-bold small mb-2">Total Dana Escrow</div>
                <div class="h4 fw-bold text-dark mb-0">Rp {{ number_format($totalDanaEscrow, 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-4">
                <div class="text-uppercase text-muted fw-bold small mb-2">Total Saldo Mengendap</div>
                <div class="h4 fw-bold text-dark mb-0">Rp {{ number_format($totalSaldoMengendap, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
