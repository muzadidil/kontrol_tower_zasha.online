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
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; color: #333; display: flex; }
        
        #wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; background: #ffffff; border-right: 1px solid #e0e0e0; overflow-y: auto; height: 100%; }
        
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
        .sidebar-divider { font-size: 0.75rem; text-transform: uppercase; color: #999; margin: 15px 20px 5px; font-weight: 700; }
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
    
    <div id="wrapper">
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
                <li>
                    <a href="{{ route('admin.finance.withdrawal') }}" class="nav-link {{ request()->is('admin/finance/withdrawal') ? 'active' : '' }}">
                        <i class="fas fa-money-bill-wave me-2"></i> Approval Penarikan
                    </a>
                </li>
                
                <li class="sidebar-divider">Operasional & Order</li>
                <li><a href="#" class="nav-link"><i class="fas fa-box me-2"></i> Semua Pesanan</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-satellite-dish me-2"></i> Live Order</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-exclamation-triangle me-2"></i> Pusat Komplain (Tiket Bantuan)</a></li>

                <li class="sidebar-divider">Menu Pengguna</li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="fas fa-users me-2"></i> Daftar Pelanggan
                    </a>
                </li>
                
                <li class="sidebar-divider">Transaksi & Layanan</li>
                <li>
                    <a href="#" class="nav-link">
                        <i class="fas fa-bolt me-2"></i> PPOB
                    </a>
                </li>

                <li class="sidebar-divider">Marketing & Promosi</li>
                <li><a href="#" class="nav-link"><i class="fas fa-image me-2"></i> Manajemen Banner</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-ticket-alt me-2"></i> Kode Voucher</a></li>

                <li class="sidebar-divider">Laporan & Pembukuan</li>
                <li><a href="#" class="nav-link"><i class="fas fa-chart-bar me-2"></i> Laporan Keuangan</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-file-download me-2"></i> Ekspor Data (Excel/PDF)</a></li>
                
                <li class="sidebar-divider">Sistem Logika</li>
                <li><a href="#" class="nav-link"><i class="fas fa-tools me-2"></i> Logika Mitra Tenaga</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-laptop me-2"></i> Logika Mitra Wfh</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-motorcycle me-2"></i> Logika Mitra Jastip</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-wrench me-2"></i> Logika Mitra Services</a></li>
                
                <li class="sidebar-divider">Konfigurasi & API</li>
                <li><a href="#" class="nav-link"><i class="fas fa-map me-2"></i> Map API</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-fire me-2"></i> Firebase</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-scroll me-2"></i> Term of Service</a></li>
                <li><a href="#" class="nav-link"><i class="fas fa-cog me-2"></i> Pengaturan Web (General Settings)</a></li>
            </ul>
        </div>
        
        <main class="flex-grow-1 p-5 overflow-auto">
            @yield('content')
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
