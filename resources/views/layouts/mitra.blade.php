<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZASHA Mitra</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root { --mitra-green: #0a5c36; --mitra-green-light: #1a7a4a; --bg-app: #f4f7fe; }
        body {
            background-color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif;
            color: #2b3674; margin: 0;
            -webkit-tap-highlight-color: transparent; -webkit-user-select: none; user-select: none;
        }
        .app-container {
            max-width: 480px; margin: 0 auto; background-color: var(--bg-app); min-height: 100vh;
            position: relative; box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding-bottom: calc(80px + env(safe-area-inset-bottom));
            padding-top: env(safe-area-inset-top); overflow-x: hidden;
        }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); }
        .nav-bottom {
            background: white; border-top: 1px solid #eee;
            max-width: 480px; margin: 0 auto; right: 0; left: 0;
            padding-bottom: env(safe-area-inset-bottom);
        }
        .nav-link-custom { color: #a3aed0; text-decoration: none; font-size: 0.65rem; font-weight: 700; padding: 10px 0; cursor: pointer; }
        .nav-link-custom.active { color: var(--mitra-green); }
        .allow-select { -webkit-user-select: text; user-select: text; }
        .badge-status-aktif { background: #d1fae5; color: #065f46; font-size: 0.65rem; padding: 4px 10px; border-radius: 50px; font-weight: 700; }
        .badge-status-nonaktif { background: #fee2e2; color: #991b1b; font-size: 0.65rem; padding: 4px 10px; border-radius: 50px; font-weight: 700; }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-container">
    @yield('content')

    <nav class="navbar fixed-bottom nav-bottom shadow-lg" id="bottom-nav">
        <div class="container-fluid d-flex justify-content-around px-0">
            <a href="{{ route('mitra.dashboard') }}" class="nav-link-custom {{ request()->routeIs('mitra.dashboard') ? 'active' : '' }} text-center flex-grow-1">
                <i class="bi bi-house-door-fill fs-5 d-block mb-1"></i>Beranda
            </a>
            <a href="{{ route('mitra.pesanan') }}" class="nav-link-custom {{ request()->routeIs('mitra.pesanan*') ? 'active' : '' }} text-center flex-grow-1">
                <i class="bi bi-clipboard-check fs-5 d-block mb-1"></i>Pesanan
            </a>
            <a href="{{ route('mitra.saldo') }}" class="nav-link-custom {{ request()->routeIs('mitra.saldo*') ? 'active' : '' }} text-center flex-grow-1">
                <i class="bi bi-wallet2 fs-5 d-block mb-1"></i>Saldo
            </a>
            <a href="{{ route('mitra.profil') }}" class="nav-link-custom {{ request()->routeIs('mitra.profil*') ? 'active' : '' }} text-center flex-grow-1">
                <i class="bi bi-person-circle fs-5 d-block mb-1"></i>Profil
            </a>
        </div>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
