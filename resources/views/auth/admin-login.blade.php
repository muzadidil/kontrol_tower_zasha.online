<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Zasha Tower</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
        .login-card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; }
        .logo-wrap { width: 56px; height: 56px; background: #005aa9; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .form-control { background: #0f172a; border: 1px solid #334155; color: #f1f5f9; border-radius: 10px; padding: 12px 16px; }
        .form-control:focus { background: #0f172a; border-color: #005aa9; color: #f1f5f9; box-shadow: 0 0 0 3px rgba(0,90,169,0.2); }
        .form-control::placeholder { color: #64748b; }
        .form-label { color: #94a3b8; font-size: 0.78rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .btn-login { background: #005aa9; color: white; border: none; border-radius: 10px; padding: 12px; font-weight: 700; width: 100%; }
        .btn-login:hover { background: #0070d4; color: white; }
        .input-group-text { background: #0f172a; border: 1px solid #334155; border-left: none; color: #64748b; cursor: pointer; }
        .error-msg { background: #450a0a; border: 1px solid #7f1d1d; color: #fca5a5; border-radius: 8px; padding: 10px 14px; font-size: 0.82rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-wrap">
            <i class="fas fa-shield-alt text-white fs-5"></i>
        </div>
        <h5 class="text-center fw-bold mb-1" style="color:#f1f5f9;">Admin Panel</h5>
        <p class="text-center mb-4" style="color:#64748b; font-size:0.82rem;">Zasha Tower — Akses Terbatas</p>

        @if($errors->any())
            <div class="error-msg mb-3">
                <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@zasha.online"
                       value="{{ old('email') }}" autofocus required>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="pw" placeholder="••••••••" required>
                    <span class="input-group-text" onclick="togglePw()">
                        <i class="fas fa-eye" id="pw-icon" style="font-size:0.85rem;"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Masuk
            </button>
        </form>
    </div>
    <script>
        function togglePw() {
            const pw = document.getElementById('pw');
            const icon = document.getElementById('pw-icon');
            if (pw.type === 'password') { pw.type = 'text'; icon.classList.replace('fa-eye','fa-eye-slash'); }
            else { pw.type = 'password'; icon.classList.replace('fa-eye-slash','fa-eye'); }
        }
    </script>
</body>
</html>
