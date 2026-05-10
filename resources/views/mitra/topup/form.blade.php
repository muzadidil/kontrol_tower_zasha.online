@extends('layouts.mitra')
@section('title', 'Topup Saldo')
@section('content')
<div class="container py-4" style="max-width: 480px;">
    <a href="{{ route('mitra.saldo') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Saldo</a>
    <h4 class="fw-bold mb-3">Topup Saldo Mitra</h4>

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('mitra.topup.store') }}" method="POST">@csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Jumlah Topup</label>
                    <input type="number" name="jumlah" class="form-control form-control-lg" min="10000" step="1000" required>
                </div>
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    @foreach([50000, 100000, 200000, 500000, 1000000] as $nom)
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="document.querySelector('[name=jumlah]').value={{ $nom }}">Rp {{ number_format($nom, 0, ',', '.') }}</button>
                    @endforeach
                </div>
                <button class="btn btn-warning w-100 fw-bold">Lanjut Bayar</button>
            </form>
        </div>
    </div>
</div>
@endsection
