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
            <a href="{{ route('admin.monitor') }}" class="nav-link {{ request()->is('admin/monitor') ? 'active' : '' }}">
                <i class="fas fa-broadcast-tower me-2"></i> Radar Mitra
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
        
        <li class="sidebar-divider">Sistem Logika</li>
        <li>
            <a href="{{ route('admin.jastip') }}" class="nav-link {{ request()->is('admin/jastip') ? 'active' : '' }}">
                <i class="fas fa-motorcycle me-2"></i> Logika Mitra Jastip
            </a>
        </li>
    </ul>
</div>
