<div id="sidebar" class="d-flex flex-column py-4">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
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