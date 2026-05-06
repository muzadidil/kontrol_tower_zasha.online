<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zasha Tower Admin</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; color: #333; display: flex; height: 100vh; margin: 0; }
        #sidebar { min-width: 260px; background: #ffffff; border-right: 1px solid #e0e0e0; }
        #sidebar .nav-link { color: #555; border-radius: 8px; margin: 4px 12px; font-weight: 500; }
        #sidebar .nav-link:hover { background: #f8f9fa; color: #005aa9; }
        #sidebar .nav-link.active { background: #005aa9; color: #fff; }
        
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-primary { background-color: #005aa9; border-color: #005aa9; }
        .btn-primary:hover { background-color: #004a8d; }
        
        .lock-screen-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.85); z-index: 9999;
            display: flex; align-items: center; justify-content: center; color: white;
        }
    </style>
</head>
<body>
    @if(isset($activeOrder))
    <div class="lock-screen-overlay">
        <div class="text-center card p-5 bg-white text-dark" style="max-width: 400px;">
            <h2 class="text-primary mb-3">Order Aktif</h2>
            <p class="mb-4">Status: <strong class="badge bg-warning text-dark">{{ $activeOrder->status }}</strong></p>
            <div class="d-grid gap-2">
                <a href="#" class="btn btn-outline-primary">Buka Maps</a>
                <a href="#" class="btn btn-outline-success">Chat WA</a>
                @if($activeOrder->status == 'Pending')
                <form action="{{ route('admin.order.updateStatus', $activeOrder->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="Menuju Lokasi">
                    <button type="submit" class="btn btn-primary w-100">SAYA MENUJU LOKASI</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div id="sidebar" class="d-flex flex-column py-4">
        <h4 class="px-4 mb-4 fw-bold text-primary">Zasha Tower</h4>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('mitra.index') }}" class="nav-link {{ request()->is('admin/mitra*') ? 'active' : '' }}">
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

    <main class="flex-grow-1 p-5 overflow-auto">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
