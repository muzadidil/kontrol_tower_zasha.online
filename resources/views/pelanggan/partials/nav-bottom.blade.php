<nav class="navbar fixed-bottom nav-bottom shadow-lg" id="bottom-nav">
    <div class="container-fluid d-flex justify-content-around px-0">
        <a href="{{ route('pelanggan.dashboard') }}" class="nav-link-custom {{ request()->is('/') ? 'active' : '' }} text-center flex-grow-1">
            <i class="bi bi-house-door-fill fs-5 d-block mb-1"></i>Beranda
        </a>
        <a href="{{ route('pelanggan.riwayat.index') }}" class="nav-link-custom {{ request()->is('riwayat*') ? 'active' : '' }} text-center flex-grow-1">
            <i class="bi bi-receipt fs-5 d-block mb-1"></i>Pesanan
        </a>
        <a href="{{ route('pelanggan.dompet') }}" class="nav-link-custom {{ request()->is('dompet*') ? 'active' : '' }} text-center flex-grow-1">
            <i class="bi bi-wallet2 fs-5 d-block mb-1"></i>Dompet
        </a>
        <a href="{{ route('pelanggan.profil') }}" class="nav-link-custom {{ request()->is('profil*') ? 'active' : '' }} text-center flex-grow-1">
            <i class="bi bi-person-circle fs-5 d-block mb-1"></i>Profil
        </a>
    </div>
</nav>