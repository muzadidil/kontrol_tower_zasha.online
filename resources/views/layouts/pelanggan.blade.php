<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ZASHA - Jastip Jember</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        :root { --zasha-blue: #002d72; --bg-app: #f4f7fe; }
        html { overflow-y: scroll; scrollbar-gutter: stable; }
        body {
            background-color: #e2e8f0; font-family: 'Plus Jakarta Sans', sans-serif; color: #2b3674; margin: 0;
            -webkit-tap-highlight-color: transparent; -webkit-user-select: none; user-select: none;
        }
        .app-container {
            max-width: 480px; margin: 0 auto; background-color: var(--bg-app); min-height: 100vh; position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.05); padding-bottom: calc(90px + env(safe-area-inset-bottom)); 
            padding-top: env(safe-area-inset-top); overflow-x: hidden;
        }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
        .profile-img { width: 45px; height: 45px; border-radius: 14px; object-fit: cover; border: 2px solid white; }
        .id-badge { background: #eef2ff; color: var(--zasha-blue); padding: 4px 10px; border-radius: 50px; font-weight: 800; font-size: 0.65rem; }
        .btn-menu { 
            border-radius: 20px; padding: 15px 5px; font-weight: 700; font-size: 0.65rem; 
            border: none; background: white; color: #2b3674; transition: 0.2s; 
            display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; box-shadow: 0 4px 12px rgba(0,0,0,0.03); text-decoration: none;
        }
        .btn-menu:active { transform: scale(0.95); background-color: #f8f9fa; }
        .btn-menu.locked { opacity: 0.5; filter: grayscale(1); cursor: not-allowed; }
        .svg-icon { display: flex; align-items: center; justify-content: center; width: 45px; height: 45px; margin-bottom: 8px; color: var(--zasha-blue); font-size: 32px; }
        
        .nav-bottom { background: white; border-top: 1px solid #eee; max-width: 480px; margin: 0 auto; right: 0; left: 0; padding-bottom: env(safe-area-inset-bottom); }
        .nav-link-custom { color: #a3aed0; text-decoration: none; font-size: 0.65rem; font-weight: 700; padding: 10px 0; cursor: pointer; }
        .nav-link-custom.active { color: var(--zasha-blue); }
        .allow-select { -webkit-user-select: text; user-select: text; }
    </style>
</head>
<body>

<div class="app-container">
    <div id="main-content">
        @yield('content')
    </div>

    @include('pelanggan.partials.nav-bottom')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>