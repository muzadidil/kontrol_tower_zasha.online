@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4 fw-bold text-dark">Main Command Center</h2>

    <!-- Quick Stats -->
    <div class="row g-4">
        <div class="col-xl-4 col-md-6">
            <div class="card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small mb-1">Total Mitra</div>
                        <div class="h3 fw-bold text-dark mb-0">{{ $totalMitra }}</div>
                    </div>
                    <div class="text-primary fs-3"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small mb-1">Total Pelanggan</div>
                        <div class="h3 fw-bold text-dark mb-0">{{ $totalPelanggan }}</div>
                    </div>
                    <div class="text-success fs-3"><i class="fas fa-user-friends"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-muted fw-bold small mb-1">Order Aktif</div>
                        <div class="h3 fw-bold text-dark mb-0">{{ $totalOrderAktif }}</div>
                    </div>
                    <div class="text-warning fs-3"><i class="fas fa-shopping-cart"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3 my-4">
        <div class="col-md-3">
            <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-primary btn-lg w-100 py-3 fw-bold">💰 Buku Kas</a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('admin.order.create') }}" class="btn btn-success btn-lg w-100 py-3 fw-bold">➕ Order Baru</a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('mitra.dashboard') }}" class="btn btn-warning btn-lg w-100 py-3 fw-bold text-white">⚡ PPOB Digiflazz</a>
        </div>
        <div class="col-md-3">
            <a href="{{ url('/admin/mitra') }}" class="btn btn-info btn-lg w-100 py-3 fw-bold text-white">👥 Manajemen Mitra</a>
        </div>
    </div>

    <!-- Latest Orders -->
    <div class="card p-4">
        <h5 class="fw-bold text-dark mb-4">5 Order Terakhir</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($latestOrders as $order)
                    <tr>
                        <td class="fw-bold">#{{ $order->id }}</td>
                        <td>{{ $order->pelanggan_id }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $order->status }}</span></td>
                        <td class="fw-bold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
