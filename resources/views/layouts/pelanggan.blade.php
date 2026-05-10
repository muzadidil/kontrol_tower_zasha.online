<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZASHA - Jastip Jember</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        /* ── Fibonacci Design System ─────────────────────────────
           Spacing  : 8 · 13 · 21 · 34 · 55px
           Radius   : 8 · 13 · 21 · 34px
           Typography: 8 · 13 · 21px
           Gold = Fibonacci's gift (φ = 1.618)
        ─────────────────────────────────────────────────────── */
        :root {
            --blue-deep:   #002d72;
            --blue-mid:    #0047b3;
            --blue-light:  #3b82f6;
            --blue-pale:   #dbeafe;
            --gold:        #f0a500;
            --gold-light:  #fef3c7;
            --gold-dark:   #b45309;
            --cyan:        #00b4d8;
            --green:       #10b981;
            --red:         #ef4444;
            --bg:          #eef2fb;
            --surface:     #ffffff;
            --text-main:   #1e293b;
            --text-muted:  #64748b;
            --text-faint:  #94a3b8;
            --border:      #e2e8f0;

            /* Fibonacci spacing */
            --s1: 8px; --s2: 13px; --s3: 21px; --s4: 34px; --s5: 55px;
            /* Fibonacci radius */
            --r1: 8px; --r2: 13px; --r3: 21px; --r4: 34px;
        }

        html { overflow-y: scroll; scrollbar-gutter: stable; }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: var(--bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            margin: 0;
            -webkit-tap-highlight-color: transparent;
            -webkit-user-select: none;
            user-select: none;
        }

        .app-wrap {
            max-width: 480px;
            margin: 0 auto;
            background: var(--bg);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 40px rgba(0,45,114,0.08);
            padding-bottom: calc(88px + env(safe-area-inset-bottom));
            overflow-x: hidden;
        }

        /* ── Surface cards ─── */
        .z-card {
            background: var(--surface);
            border-radius: var(--r3);
            border: none;
            box-shadow: 0 2px 16px rgba(0,45,114,0.06);
        }

        /* ── Typography helpers ─── */
        .label-xs { font-size: 8px;  font-weight: 800; text-transform: uppercase; letter-spacing: .8px; }
        .label-sm { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; }
        .label-md { font-size: 13px; font-weight: 700; }
        .allow-select { -webkit-user-select: text; user-select: text; }

        /* ── Bottom nav ─── */
        .nav-bottom {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            max-width: 480px;
            margin: 0 auto;
            background: var(--surface);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: stretch;
            padding: var(--s1) 0;
            padding-bottom: calc(var(--s1) + env(safe-area-inset-bottom));
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(0,45,114,0.08);
        }
        .nav-item-z {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            text-decoration: none;
            color: var(--text-faint);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .3px;
            padding: var(--s1) 0;
            position: relative;
            transition: color .2s;
        }
        .nav-item-z i { font-size: 22px; transition: transform .2s; }
        .nav-item-z.active {
            color: var(--blue-deep);
        }
        .nav-item-z.active i { transform: translateY(-2px); }
        .nav-item-z.active::before {
            content: '';
            position: absolute;
            top: 0; left: 50%;
            transform: translateX(-50%);
            width: 34px; height: 3px;
            background: var(--gold);
            border-radius: 0 0 var(--r1) var(--r1);
        }

        /* ── Buttons ─── */
        .btn-z-primary {
            background: var(--blue-deep);
            color: white;
            border: none;
            border-radius: var(--r4);
            font-weight: 800;
            font-size: 13px;
            padding: var(--s2) var(--s3);
            transition: .2s;
        }
        .btn-z-primary:hover { background: var(--blue-mid); color: white; }
        .btn-z-gold {
            background: var(--gold);
            color: var(--blue-deep);
            border: none;
            border-radius: var(--r4);
            font-weight: 800;
            font-size: 13px;
            padding: var(--s2) var(--s3);
            transition: .2s;
        }
        .btn-z-ghost {
            background: transparent;
            color: var(--blue-deep);
            border: 1.5px solid var(--border);
            border-radius: var(--r4);
            font-weight: 700;
            font-size: 13px;
            padding: calc(var(--s2) - 1.5px) var(--s3);
        }

        /* ── Badge ─── */
        .z-badge {
            background: var(--blue-pale);
            color: var(--blue-deep);
            border-radius: var(--r4);
            font-size: 8px;
            font-weight: 800;
            padding: 3px var(--s1);
            letter-spacing: .4px;
            text-transform: uppercase;
        }
        .z-badge-gold { background: var(--gold-light); color: var(--gold-dark); }
        .z-badge-green { background: #d1fae5; color: #065f46; }
        .z-badge-red   { background: #fee2e2; color: #991b1b; }

        /* ── Menu icon ─── */
        .menu-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--surface);
            border-radius: var(--r3);
            padding: var(--s2) var(--s1);
            gap: var(--s1);
            text-decoration: none;
            color: var(--text-main);
            font-size: 9px;
            font-weight: 700;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,45,114,0.06);
            transition: .15s;
            min-height: 88px;
        }
        .menu-btn:active { transform: scale(.94); }
        .menu-btn.locked { opacity: .45; filter: grayscale(1); }
        .menu-icon-wrap {
            width: 44px; height: 44px;
            border-radius: var(--r2);
            background: var(--blue-pale);
            display: flex; align-items: center; justify-content: center;
            color: var(--blue-deep);
        }
        .menu-icon-wrap svg, .menu-icon-wrap i { width: 22px; height: 22px; font-size: 22px; }

        /* ── Misc ─── */
        .divider-label {
            display: flex; align-items: center; gap: var(--s2);
            font-size: 10px; font-weight: 800; color: var(--text-faint);
            text-transform: uppercase; letter-spacing: .6px;
            margin: var(--s4) 0 var(--s2);
        }
        .divider-label::after { content:''; flex:1; height:1px; background:var(--border); }
    </style>
</head>
<body>
<div class="app-wrap">
    @yield('content')
    @include('pelanggan.partials.nav-bottom')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
