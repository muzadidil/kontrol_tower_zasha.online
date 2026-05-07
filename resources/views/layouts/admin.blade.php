<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zasha Tower Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; color: #333; display: flex; }
        #wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        #sidebar { min-width: 260px; max-width: 260px; background: #ffffff; border-right: 1px solid #e0e0e0; overflow-y: auto; height: 100%; }
        #sidebar .nav-link { color: #555; border-radius: 8px; margin: 4px 12px; font-weight: 500; }
        #sidebar .nav-link:hover { background: #f8f9fa; color: #005aa9; }
        #sidebar .nav-link.active { background: #005aa9; color: #fff; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .btn-primary { background-color: #005aa9; border-color: #005aa9; }
        .lock-screen-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white; }
        .sidebar-divider { font-size: 0.75rem; text-transform: uppercase; color: #999; margin: 15px 20px 5px; font-weight: 700; }
    </style>
</head>
<body>

    @include('admin.partials.lock-screen')
    
    <div id="wrapper">
        @include('admin.partials.sidebar')
        
        <main class="flex-grow-1 p-5 overflow-auto">
            @yield('content')
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>