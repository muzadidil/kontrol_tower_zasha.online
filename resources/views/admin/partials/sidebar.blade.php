<div id="sidebar" class="d-flex flex-column py-4">
    <ul class="nav nav-pills flex-column mb-auto">

        {{-- ── DASHBOARD ─────────────────────────────────────── --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') && !request()->is('admin/dashboard/finance') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>

        {{-- ── MITRA ────────────────────────────────────────── --}}
        <li class="sidebar-divider">Mitra</li>

        @php
            $isMitraActive = request()->is('admin/mitra*')
                          || request()->is('admin/verification*')
                          || request()->is('admin/roles*')
                          || request()->is('admin/tarif*');
        @endphp
        <li class="nav-item">
            <a href="#mitraMenu" data-bs-toggle="collapse"
               class="nav-link d-flex justify-content-between align-items-center {{ $isMitraActive ? 'active' : '' }}"
               aria-expanded="{{ $isMitraActive ? 'true' : 'false' }}">
                <span><i class="fas fa-users me-2"></i> Mitra & Akses</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ $isMitraActive ? 'show' : '' }}" id="mitraMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li>
                        <a href="{{ route('admin.mitra.index') }}"
                           class="nav-link py-1 small {{ request()->is('admin/mitra*') && !request()->is('admin/mitra/dashboard*') ? 'active' : '' }}">
                            <i class="fas fa-users me-2"></i> Manajemen Mitra
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.verification.index') }}"
                           class="nav-link py-1 small {{ request()->is('admin/verification*') ? 'active' : '' }}">
                            <i class="fas fa-user-check me-2"></i> Verifikasi Dokumen
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.roles.index') }}"
                           class="nav-link py-1 small {{ request()->is('admin/roles*') ? 'active' : '' }}">
                            <i class="fas fa-shield-alt me-2"></i> Role & Akses
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tarif.index') }}"
                           class="nav-link py-1 small {{ request()->is('admin/tarif*') ? 'active' : '' }}">
                            <i class="fas fa-cash-register me-2"></i> Tarif Layanan
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- ── PESANAN (gabungan: Live Monitor + Modul + Arsip) ── --}}
        <li class="sidebar-divider">Pesanan</li>

        @php
            $isPesananActive = request()->is('admin/orders*') || request()->is('admin/wfh*') || request()->is('admin/jastip*') || request()->is('admin/tenaga*') || request()->is('admin/service*');
        @endphp
        <li class="nav-item">
            <a href="#pesananMenu" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center {{ $isPesananActive ? 'active' : '' }}" aria-expanded="{{ $isPesananActive ? 'true' : 'false' }}">
                <span><i class="fas fa-receipt me-2"></i> Monitoring Order</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ $isPesananActive ? 'show' : '' }}" id="pesananMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="nav-link py-1 small {{ request()->is('admin/orders') && !request()->is('admin/orders/arsip*') ? 'active' : '' }}">
                            <i class="fas fa-globe me-2"></i> Live (Semua)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.wfh.index') }}" class="nav-link py-1 small {{ request()->is('admin/wfh*') ? 'active' : '' }}">
                            <i class="fas fa-laptop-code me-2"></i> WFH (Digital)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.jastip.index') }}" class="nav-link py-1 small {{ request()->is('admin/jastip-monitoring*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag me-2"></i> Jastip
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.tenaga.index') }}" class="nav-link py-1 small {{ request()->is('admin/tenaga*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie me-2"></i> Tenaga
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.service.index') }}" class="nav-link py-1 small {{ request()->is('admin/service*') ? 'active' : '' }}">
                            <i class="fas fa-tools me-2"></i> Service
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a href="{{ route('admin.orders.arsip') }}" class="nav-link {{ request()->is('admin/orders/arsip*') ? 'active' : '' }}">
                <i class="fas fa-archive me-2"></i> Arsip Pesanan
            </a>
        </li>

        {{-- ── KEUANGAN ────────────────────────────────────── --}}
        <li class="sidebar-divider">Keuangan</li>
        <li>
            <a href="{{ route('admin.finance.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard/finance') ? 'active' : '' }}">
                <i class="fas fa-wallet me-2"></i> Ringkasan Keuangan
            </a>
        </li>

        {{-- Topup & Deposit digabung (sama-sama nambah saldo) --}}
        @php
            $isSaldoActive = request()->is('admin/finance/topup*') || request()->is('admin/finance/deposit*');
        @endphp
        <li class="nav-item">
            <a href="#saldoMenu" data-bs-toggle="collapse" class="nav-link d-flex justify-content-between align-items-center {{ $isSaldoActive ? 'active' : '' }}" aria-expanded="{{ $isSaldoActive ? 'true' : 'false' }}">
                <span><i class="fas fa-cash-register me-2"></i> Topup & Deposit</span>
                <i class="fas fa-chevron-down small"></i>
            </a>
            <div class="collapse {{ $isSaldoActive ? 'show' : '' }}" id="saldoMenu">
                <ul class="nav flex-column ms-3 mt-1">
                    <li>
                        <a href="{{ route('admin.finance.topup.index') }}" class="nav-link py-1 small {{ request()->is('admin/finance/topup*') ? 'active' : '' }}">
                            <i class="fas fa-arrow-up me-2"></i> Konfirmasi Topup
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.finance.deposit') }}" class="nav-link py-1 small {{ request()->is('admin/finance/deposit*') ? 'active' : '' }}">
                            <i class="fas fa-plus-circle me-2"></i> Deposit Manual
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a href="{{ route('admin.finance.withdrawal') }}" class="nav-link {{ request()->is('admin/finance/withdrawal') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave me-2"></i> Approval Penarikan
            </a>
        </li>

        {{-- ── PRODUK DIGITAL ──────────────────────────────── --}}
        <li class="sidebar-divider">Produk Digital</li>
        <li>
            <a href="{{ route('admin.ppob.index') }}" class="nav-link {{ request()->is('admin/ppob*') ? 'active' : '' }}">
                <i class="fas fa-mobile-alt me-2"></i> Monitoring PPOB
            </a>
        </li>
        {{-- Game Top-Up REMOVED: fitur dihapus saat cleanup tabel kategoris/layanans --}}

        {{-- ── SISTEM ──────────────────────────────────────── --}}
        <li class="sidebar-divider">Sistem</li>
        <li>
            <a href="{{ route('admin.monitor') }}" class="nav-link {{ request()->is('admin/monitor*') ? 'active' : '' }}">
                <i class="fas fa-desktop me-2"></i> Session Monitor
            </a>
        </li>
        <li>
            <a href="{{ route('admin.settings', ['tab' => 'api']) }}" class="nav-link {{ request()->is('admin/settings*') ? 'active' : '' }}">
                <i class="fas fa-cog me-2"></i> Pengaturan
            </a>
        </li>
    </ul>
</div>
