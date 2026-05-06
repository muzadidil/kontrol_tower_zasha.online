@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3>Dashboard Keuangan</h3>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card p-3">
                <h6>Total Omzet</h6>
                <h5>Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6>Total Cuan Zasha (5%)</h6>
                <h5>Rp {{ number_format($totalCuanZasha, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6>Total Profit PPOB</h6>
                <h5>Rp {{ number_format($totalProfitPpob, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6>Total Dana Escrow</h6>
                <h5>Rp {{ number_format($totalDanaEscrow, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-md-3 mt-3">
            <div class="card p-3">
                <h6>Total Saldo Mengendap</h6>
                <h5>Rp {{ number_format($totalSaldoMengendap, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
</div>
@endsection
