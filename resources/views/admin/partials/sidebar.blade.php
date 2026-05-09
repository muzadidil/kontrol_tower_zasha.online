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
        <li class="sidebar-divider">Keuangan</li>
        <li>
            <a href="{{ route('admin.finance.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard/finance') ? 'active' : '' }}">
                <i class="fas fa-wallet me-2"></i> Ringkasan Keuangan
            </a>
        </li>
        <!-- MENU BARU: Konfirmasi Topup -->
        <li>
            <a href="{{ route('admin.finance.topup.index') }}" class="nav-link {{ request()->is('admin/finance/topup*') ? 'active' : '' }}">
                <i class="fas fa-cash-register me-2"></i> Konfirmasi Topup
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
        <li>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->is('admin/orders') ? 'active' : '' }}">
                <i class="fas fa-receipt me-2"></i> Global Monitoring
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.arsip') }}" class="nav-link {{ request()->is('admin/orders/arsip*') ? 'active' : '' }}">
                <i class="fas fa-archive me-2"></i> Arsip Pesanan
            </a>
        </li>

    </ul>
</div>
