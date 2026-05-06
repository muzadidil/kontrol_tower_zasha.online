<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zasha Tower Admin</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; height: 100vh; }
        #sidebar { min-width: 250px; background: #343a40; color: #fff; }
        #sidebar .nav-link { color: #ccc; }
        #sidebar .nav-link:hover { color: #fff; background: #495057; }
        #sidebar .active { color: #fff; background: #0d6efd; }
        
        .lock-screen-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.9);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
    </style>
</head>
<body>
    @if(isset($activeOrder))
    <div class="lock-screen-overlay">
        <div class="text-center">
            <h2>Order Sedang Berjalan</h2>
            <p>Status: {{ $activeOrder->status }}</p>
            <div class="d-grid gap-3">
                <a href="#" class="btn btn-primary btn-lg">Buka Maps</a>
                <a href="#" class="btn btn-success btn-lg">Chat WA</a>
                @if($activeOrder->status == 'Pending')
                <form action="{{ route('admin.order.updateStatus', $activeOrder->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Menuju Lokasi">
                    <button type="submit" class="btn btn-warning btn-lg">SAYA MENUJU LOKASI</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div id="sidebar" class="p-3">
        <h4 class="text-white text-center">Zasha Tower</h4>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="#" class="nav-link">
                    <i class="fas fa-users me-2"></i> Manajemen Mitra
                </a>
            </li>
            <li>
                <a href="{{ route('admin.finance.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard/finance') ? 'active' : '' }}">
                    <i class="fas fa-wallet me-2"></i> Keuangan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.finance.deposit') }}" class="nav-link {{ request()->is('admin/finance/deposit') ? 'active' : '' }}">
                    <i class="fas fa-plus-circle me-2"></i> Deposit Manual
                </a>
            </li>

        </ul>
    </div>

    <main class="flex-grow-1 p-4 bg-light">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
