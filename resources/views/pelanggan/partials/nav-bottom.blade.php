<nav class="nav-bottom" id="bottom-nav">
    <a href="{{ route('pelanggan.dashboard') }}"
       class="nav-item-z {{ request()->routeIs('pelanggan.dashboard') ? 'active' : '' }}">
        <i class="bi {{ request()->routeIs('pelanggan.dashboard') ? 'bi-house-door-fill' : 'bi-house-door' }}"></i>
        Beranda
    </a>
    <a href="{{ route('pelanggan.riwayat.index') }}"
       class="nav-item-z {{ request()->routeIs('pelanggan.riwayat.*') ? 'active' : '' }}">
        <i class="bi {{ request()->routeIs('pelanggan.riwayat.*') ? 'bi-receipt-cutoff' : 'bi-receipt' }}"></i>
        Pesanan
    </a>
    <a href="{{ route('pelanggan.dompet') }}"
       class="nav-item-z {{ request()->routeIs('pelanggan.dompet') ? 'active' : '' }}">
        <i class="bi {{ request()->routeIs('pelanggan.dompet') ? 'bi-wallet-fill' : 'bi-wallet2' }}"></i>
        Dompet
    </a>
    <a href="{{ route('pelanggan.profil') }}"
       class="nav-item-z {{ request()->routeIs('pelanggan.profil') ? 'active' : '' }}">
        <i class="bi {{ request()->routeIs('pelanggan.profil') ? 'bi-person-fill' : 'bi-person' }}"></i>
        Profil
    </a>
</nav>
