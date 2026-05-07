@extends('layouts.admin')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-zasha p-3 border-start border-primary border-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                    <i class="fa-solid fa-wallet fs-4 text-primary"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted small mb-1 fw-bold">Total Omzet</p>
                    <h5 class="mb-0 fw-bold">Rp 12.540.000</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-zasha p-3 border-start border-success border-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                    <i class="fa-solid fa-users fs-4 text-success"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted small mb-1 fw-bold">Mitra Aktif</p>
                    <h5 class="mb-0 fw-bold">84 Orang</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-zasha p-3 border-start border-warning border-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-warning bg-opacity-10 p-3 rounded-3">
                    <i class="fa-solid fa-clock-rotate-left fs-4 text-warning"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted small mb-1 fw-bold">Pending Order</p>
                    <h5 class="mb-0 fw-bold">12 Pesanan</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-zasha p-3 border-start border-info border-4">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                    <i class="fa-solid fa-chart-line fs-4 text-info"></i>
                </div>
                <div class="flex-grow-1 ms-3">
                    <p class="text-muted small mb-1 fw-bold">Profit (Bulan Ini)</p>
                    <h5 class="mb-0 fw-bold">Rp 3.200.000</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card card-zasha p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0">Tren Pesanan Mingguan</h6>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Mei 2026
                    </button>
                </div>
            </div>
            <canvas id="orderChart" height="300"></canvas>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card card-zasha p-4 h-100">
            <h6 class="fw-bold mb-4">Aktivitas Terakhir</h6>
            <div class="list-group list-group-flush">
                <div class="list-group-item px-0 border-0 mb-3">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=BU&background=random" class="rounded-circle me-3" width="40">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 small fw-bold">Budi Utomo</h6>
                            <small class="text-muted">Order Jastip Food • 2m ago</small>
                        </div>
                        <span class="badge bg-light text-primary rounded-pill">Rp 45k</span>
                    </div>
                </div>
                <div class="list-group-item px-0 border-0 mb-3">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=AM&background=random" class="rounded-circle me-3" width="40">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 small fw-bold">Anisa Maharani</h6>
                            <small class="text-muted">Service AC • 15m ago</small>
                        </div>
                        <span class="badge bg-light text-primary rounded-pill">Rp 150k</span>
                    </div>
                </div>
                <div class="list-group-item px-0 border-0">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name=RK&background=random" class="rounded-circle me-3" width="40">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 small fw-bold">Rizky Perdana</h6>
                            <small class="text-muted">PPOB Listrik • 1h ago</small>
                        </div>
                        <span class="badge bg-light text-primary rounded-pill">Rp 200k</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('pelanggan.riwayat.index') }}" class="btn btn-light w-100 mt-4 fw-bold small rounded-pill">Lihat Semua</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('orderChart').getContext('2d');
    
    // Gradasi Warna untuk Area Chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 90, 169, 0.2)');
    gradient.addColorStop(1, 'rgba(0, 90, 169, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            datasets: [{
                label: 'Jumlah Pesanan',
                data: [12, 19, 15, 25, 22, 30, 28],
                borderColor: '#005aa9',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#005aa9'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5], color: '#e0e0e0', drawBorder: false }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush