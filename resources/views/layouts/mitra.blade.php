<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZASHA Mitra</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════════════
           FIBONACCI / GOLDEN RATIO DESIGN SYSTEM
           Sequence: 5, 8, 13, 21, 34, 55, 89, 144 (px)
           Ratio φ = 1.618
        ═══════════════════════════════════════════════════════════ */
        :root {
            /* Spacing — Fibonacci */
            --fib-1: 5px;
            --fib-2: 8px;
            --fib-3: 13px;
            --fib-4: 21px;
            --fib-5: 34px;
            --fib-6: 55px;
            --fib-7: 89px;
            --fib-8: 144px;

            /* Border radius */
            --r-sm: 8px;
            --r-md: 13px;
            --r-lg: 21px;
            --r-xl: 34px;
            --r-pill: 999px;

            /* Type scale — golden modular */
            --t-xxs: 0.625rem;
            --t-xs:  0.75rem;
            --t-sm:  0.8125rem;
            --t-base:0.875rem;
            --t-md:  1rem;
            --t-lg:  1.3125rem;
            --t-xl:  1.6875rem;
            --t-2xl: 2.125rem;

            /* Brand — Blue Laut palette (monochromatic) */
            --mitra-blue: #005aa9;
            --mitra-blue-dark: #003d75;
            --mitra-blue-mid: #0087d9;
            --mitra-blue-light: #b3dafd;
            --mitra-blue-soft: #e6f4ff;
            --mitra-blue-tint: #f0f7ff;
            /* Aliases — view existing masih reference nama lama, value-nya sekarang biru */
            --mitra-green: var(--mitra-blue);
            --mitra-green-light: var(--mitra-blue-dark);
            --mitra-gold: var(--mitra-blue-mid);
            --mitra-gold-soft: var(--mitra-blue-soft);
            --bg-app: #f8fbff;
            --surface: #ffffff;
            --ink: #1a2332;
            --ink-soft: #6b7280;
            --line: #e1ecf7;

            /* Layout */
            --nav-height: 72px;
            --container-max: 480px;
        }

        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; overflow-x: hidden; }

        body {
            background-color: var(--mitra-blue-light);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            -webkit-tap-highlight-color: transparent;
            -webkit-user-select: none;
            user-select: none;
            min-height: 100vh;
            overscroll-behavior: none;
            font-size: var(--t-base);
            line-height: 1.5;
        }

        .app-container {
            max-width: var(--container-max);
            width: 100%;
            margin: 0 auto;
            background-color: var(--bg-app);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 var(--fib-5) rgba(0,0,0,0.06);
            /* Buffer fib-8 + fib-6 (144 + 55 = 199px) agar tombol/menu terakhir tidak tertutup nav-bottom */
            padding-bottom: calc(var(--nav-height) + var(--fib-8) + var(--fib-6) + env(safe-area-inset-bottom));
            padding-top: env(safe-area-inset-top);
            overflow-x: hidden;
        }

        /* ── Card hierarchy ── */
        .card-custom {
            border: none;
            border-radius: var(--r-lg);
            background: var(--surface);
            box-shadow: 0 var(--fib-1) var(--fib-3) rgba(0,0,0,0.04);
        }
        .card-elevated {
            border-radius: var(--r-xl);
            box-shadow: 0 var(--fib-2) var(--fib-5) rgba(0,0,0,0.06);
        }

        /* ── Hero with golden proportion ── */
        .hero-mitra {
            background: linear-gradient(135deg, var(--mitra-green) 0%, var(--mitra-green-light) 100%);
            color: #fff;
            border-radius: var(--r-xl);
            padding: var(--fib-4);
            position: relative;
            overflow: hidden;
        }
        .hero-mitra::before {
            content: '';
            position: absolute;
            top: calc(var(--fib-7) * -1);
            right: calc(var(--fib-7) * -1);
            width: var(--fib-8);
            height: var(--fib-8);
            background: rgba(179, 218, 253, 0.25);
            border-radius: 50%;
        }
        .hero-mitra::after {
            content: '';
            position: absolute;
            bottom: calc(var(--fib-6) * -1);
            left: calc(var(--fib-5) * -1);
            width: var(--fib-7);
            height: var(--fib-7);
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .hero-content { position: relative; z-index: 2; }

        /* ── Avatar fibonacci sizes ── */
        .avatar-21 { width: var(--fib-4); height: var(--fib-4); }
        .avatar-34 { width: var(--fib-5); height: var(--fib-5); border-radius: var(--r-md); }
        .avatar-55 { width: var(--fib-6); height: var(--fib-6); border-radius: var(--r-lg); }
        .avatar-89 { width: var(--fib-7); height: var(--fib-7); border-radius: var(--r-xl); }

        /* ── Page section spacing ── */
        .page-pad { padding: var(--fib-4); }
        .stack-3 > * + * { margin-top: var(--fib-3); }
        .stack-4 > * + * { margin-top: var(--fib-4); }
        .stack-5 > * + * { margin-top: var(--fib-5); }

        /* ── Typography ── */
        .t-xxs { font-size: var(--t-xxs); }
        .t-xs  { font-size: var(--t-xs); }
        .t-sm  { font-size: var(--t-sm); }
        .t-lg  { font-size: var(--t-lg); }
        .t-xl  { font-size: var(--t-xl); }
        .t-2xl { font-size: var(--t-2xl); }
        .label-up {
            font-size: var(--t-xxs);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--ink-soft);
        }

        /* ── Status badges (monochromatic blue tints) ── */
        .badge-status-aktif {
            background: var(--mitra-blue-soft);
            color: var(--mitra-blue-dark);
            font-size: var(--t-xxs);
            padding: var(--fib-1) var(--fib-3);
            border-radius: var(--r-pill);
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .badge-status-nonaktif {
            background: #e1ecf7;
            color: #6b7280;
            font-size: var(--t-xxs);
            padding: var(--fib-1) var(--fib-3);
            border-radius: var(--r-pill);
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* ── Buttons ── */
        .btn-mitra-primary {
            background: linear-gradient(135deg, var(--mitra-green), var(--mitra-green-light));
            color: #fff;
            border: none;
            border-radius: var(--r-pill);
            font-weight: 700;
            font-size: var(--t-sm);
            padding: var(--fib-2) var(--fib-4);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-mitra-primary:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 var(--fib-2) var(--fib-4) rgba(0,90,169,0.3);
        }
        .btn-mitra-ghost {
            background: rgba(255,255,255,0.18);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: var(--r-pill);
            font-weight: 700;
            font-size: var(--t-sm);
            padding: var(--fib-2) var(--fib-4);
            backdrop-filter: blur(8px);
        }
        .btn-mitra-ghost:hover { background: rgba(255,255,255,0.28); color: #fff; }

        /* ── Menu tile ── */
        .menu-tile {
            display: flex;
            align-items: center;
            gap: var(--fib-3);
            padding: var(--fib-3);
            background: var(--surface);
            border-radius: var(--r-lg);
            text-decoration: none;
            color: var(--ink);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 var(--fib-1) var(--fib-3) rgba(0,0,0,0.03);
        }
        .menu-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 var(--fib-2) var(--fib-4) rgba(0,0,0,0.08);
            color: var(--ink);
        }
        .menu-tile-icon {
            width: var(--fib-5); height: var(--fib-5);
            border-radius: var(--r-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: var(--t-md);
            flex-shrink: 0;
        }

        .allow-select { -webkit-user-select: text; user-select: text; }

        /* ── Bootstrap utility override untuk konsistensi biru di modul (wfh/jastip/tenaga/service) ── */
        .app-container .btn-warning,
        .app-container .btn-warning:focus,
        .app-container .btn-warning:active {
            background-color: var(--mitra-blue) !important;
            border-color: var(--mitra-blue) !important;
            color: #fff !important;
        }
        .app-container .btn-warning:hover {
            background-color: var(--mitra-blue-dark) !important;
            border-color: var(--mitra-blue-dark) !important;
        }
        .app-container .btn-outline-warning,
        .app-container .btn-outline-warning:focus {
            color: var(--mitra-blue) !important;
            border-color: var(--mitra-blue) !important;
            background: transparent !important;
        }
        .app-container .btn-outline-warning:hover,
        .app-container .btn-outline-warning:active {
            background: var(--mitra-blue) !important;
            color: #fff !important;
            border-color: var(--mitra-blue) !important;
        }
        .app-container .text-warning { color: var(--mitra-blue) !important; }
        .app-container .bg-warning {
            background-color: var(--mitra-blue-soft) !important;
            color: var(--mitra-blue-dark) !important;
        }
        .app-container .border-warning { border-color: var(--mitra-blue-light) !important; }

        .app-container .btn-success {
            background-color: var(--mitra-blue-mid) !important;
            border-color: var(--mitra-blue-mid) !important;
        }
        .app-container .btn-success:hover {
            background-color: var(--mitra-blue) !important;
            border-color: var(--mitra-blue) !important;
        }
        .app-container .text-success { color: var(--mitra-blue) !important; }

        .app-container .btn-info,
        .app-container .btn-info:focus {
            background-color: var(--mitra-blue-mid) !important;
            border-color: var(--mitra-blue-mid) !important;
            color: #fff !important;
        }
        .app-container .text-info { color: var(--mitra-blue-mid) !important; }

        .app-container .btn-primary,
        .app-container .btn-primary:focus {
            background-color: var(--mitra-blue) !important;
            border-color: var(--mitra-blue) !important;
        }
        .app-container .btn-primary:hover {
            background-color: var(--mitra-blue-dark) !important;
            border-color: var(--mitra-blue-dark) !important;
        }
        .app-container .text-primary { color: var(--mitra-blue) !important; }

        /* Form focus state — semua input di halaman mitra konsisten biru */
        .app-container .form-control:focus,
        .app-container .form-select:focus {
            border-color: var(--mitra-blue) !important;
            box-shadow: 0 0 0 0.2rem rgba(0,90,169,0.15) !important;
        }

        /* Alert tints */
        .app-container .alert-warning {
            background: var(--mitra-blue-soft) !important;
            border-color: var(--mitra-blue-light) !important;
            color: var(--mitra-blue-dark) !important;
        }
        .app-container .alert-info {
            background: var(--mitra-blue-tint) !important;
            border-color: var(--mitra-blue-light) !important;
            color: var(--mitra-blue-dark) !important;
        }
        .app-container .alert-success {
            background: var(--mitra-blue-soft) !important;
            border-color: var(--mitra-blue-light) !important;
            color: var(--mitra-blue-dark) !important;
        }

        /* Badge tints — semantic colors tetap recognizable tapi via blue tints */
        .app-container .badge.bg-warning,
        .app-container .badge.text-bg-warning {
            background-color: var(--mitra-blue-soft) !important;
            color: var(--mitra-blue-dark) !important;
        }
        .app-container .badge.bg-success,
        .app-container .badge.text-bg-success {
            background-color: var(--mitra-blue) !important;
            color: #fff !important;
        }
        .app-container .badge.bg-info,
        .app-container .badge.text-bg-info {
            background-color: var(--mitra-blue-mid) !important;
            color: #fff !important;
        }

        /* ── Bottom Nav ── */
        .nav-bottom {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) translateZ(0);
            width: 100%;
            max-width: var(--container-max);
            height: var(--nav-height);
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(13px);
            -webkit-backdrop-filter: blur(13px);
            border-top: 1px solid var(--line);
            padding: 0;
            padding-bottom: env(safe-area-inset-bottom);
            margin: 0;
            box-shadow: 0 calc(var(--fib-1) * -1) var(--fib-4) rgba(0,0,0,0.05);
            z-index: 1030;
            will-change: transform;
            backface-visibility: hidden;
        }
        .nav-bottom-inner {
            display: flex; justify-content: space-around; align-items: stretch;
            width: 100%; height: 100%;
        }
        .nav-link-custom {
            color: #a8b3c3;
            text-decoration: none;
            font-size: var(--t-xxs);
            font-weight: 700;
            cursor: pointer;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            flex: 1 1 0;
            padding: var(--fib-2) 0;
            transition: color 0.2s ease;
            position: relative;
        }
        .nav-link-custom i {
            font-size: 1.35rem;
            margin-bottom: var(--fib-1);
            transition: transform 0.2s ease;
        }
        .nav-link-custom:hover { color: var(--mitra-green-light); }
        .nav-link-custom.active { color: var(--mitra-green); }
        .nav-link-custom.active i { transform: scale(1.15); }
        .nav-link-custom.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 30%;
            right: 30%;
            height: var(--fib-1);
            background: linear-gradient(90deg, var(--mitra-green), var(--mitra-gold));
            border-radius: 0 0 var(--fib-1) var(--fib-1);
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-container">
    @yield('content')
</div>

<nav class="nav-bottom" id="bottom-nav">
    <div class="nav-bottom-inner">
        <a href="{{ route('mitra.dashboard') }}" class="nav-link-custom {{ request()->routeIs('mitra.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house-door-fill"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('mitra.pesanan') }}" class="nav-link-custom {{ request()->routeIs('mitra.pesanan*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i>
            <span>Pesanan</span>
        </a>
        <a href="{{ route('mitra.saldo') }}" class="nav-link-custom {{ request()->routeIs('mitra.saldo*') ? 'active' : '' }}">
            <i class="bi bi-wallet2"></i>
            <span>Saldo</span>
        </a>
        <a href="{{ route('mitra.profil') }}" class="nav-link-custom {{ request()->routeIs('mitra.profil*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>
            <span>Profil</span>
        </a>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
