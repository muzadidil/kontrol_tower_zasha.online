@extends('layouts.guest')

@section('content')
<style>
    body { background: linear-gradient(135deg, #e6f4ff 0%, #b3dafd 100%); min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
    .auth-wrapper { min-height: 100vh; display: flex; align-items: center; padding: 30px 0; }
    .auth-card { background: #fff; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,90,169,0.15); overflow: hidden; }
    .auth-hero {
        background: linear-gradient(135deg, #005aa9 0%, #003d75 100%);
        color: #fff; padding: 50px 40px; position: relative; overflow: hidden;
    }
    .auth-hero::before {
        content: ''; position: absolute; top: -50px; right: -50px;
        width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;
    }
    .auth-hero::after {
        content: ''; position: absolute; bottom: -80px; left: -80px;
        width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%;
    }
    .feature-list li { padding: 8px 0; list-style: none; font-size: 14px; }
    .feature-list i {
        background: rgba(255,255,255,0.2); width: 28px; height: 28px;
        border-radius: 50%; display: inline-flex; align-items: center;
        justify-content: center; margin-right: 10px;
    }
    .form-control:focus { border-color: #005aa9; box-shadow: 0 0 0 0.2rem rgba(0,90,169,0.15); }
    .btn-mitra {
        background: linear-gradient(135deg, #005aa9 0%, #003d75 100%);
        border: none; color: #fff; font-weight: 700; padding: 13px;
        border-radius: 12px; transition: all 0.3s;
    }
    .btn-mitra:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,90,169,0.3); color: #fff; }
    .input-group-text { background: #f0f7ff; border-color: #b3dafd; font-weight: 700; color: #005aa9; }
    .auth-link { color: #005aa9; font-weight: 600; text-decoration: none; }
    .auth-link:hover { color: #003d75; text-decoration: underline; }
    .role-badge {
        display: inline-flex; align-items: center; background: rgba(255,255,255,0.2);
        padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-bottom: 16px;
    }
</style>

<div class="container auth-wrapper">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="auth-card row g-0">
                {{-- Hero (Left) --}}
                <div class="col-md-5 auth-hero d-none d-md-flex flex-column">
                    <div class="position-relative" style="z-index: 2;">
                        <span class="role-badge"><i class="bi bi-briefcase-fill me-1"></i> MITRA ZASHA</span>
                        <h2 class="fw-bold mb-3">Halo Mitra Pejuang!</h2>
                        <p class="opacity-75 mb-4">Kelola order, atur jadwal, dan pantau penghasilan Anda dalam satu dashboard.</p>
                        <ul class="feature-list ps-0 mb-0">
                            <li><i class="bi bi-cash-stack"></i> Komisi transparan, bayar setiap order</li>
                            <li><i class="bi bi-bell"></i> Notifikasi real-time untuk order baru</li>
                            <li><i class="bi bi-graph-up-arrow"></i> Statistik & rating dari pelanggan</li>
                            <li><i class="bi bi-shield-check"></i> Pencairan saldo via bank/e-wallet</li>
                        </ul>
                    </div>
                </div>

                {{-- Form (Right) --}}
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logo_zasha.png') }}" alt="Zasha" style="height: 60px;" onerror="this.style.display='none'">
                            <h3 class="fw-bold mt-3 mb-1">Login Mitra</h3>
                            <p class="text-muted small">Masuk untuk akses dashboard mitra</p>
                        </div>

                        @if(session('error')) <div class="alert alert-danger small">{{ session('error') }}</div> @endif

                        <form action="{{ route('mitra.login.submit') }}" method="POST">@csrf

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="tel" id="wa_input" inputmode="numeric" pattern="[0-9]*"
                                        class="form-control @error('nomor_wa') is-invalid @enderror"
                                        placeholder="8xxxxxxxxxx" maxlength="13" autofocus>
                                    <input type="hidden" name="nomor_wa" id="wa_hidden" value="{{ old('nomor_wa') }}">
                                </div>
                                @error('nomor_wa') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control"
                                        required placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label small" for="remember">Ingat saya</label>
                                </div>
                                <a href="#" class="auth-link small">Lupa password?</a>
                            </div>

                            <button type="submit" class="btn btn-mitra w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login Mitra
                            </button>
                        </form>

                        <div class="text-center mt-4 small">
                            Belum jadi mitra?
                            <a href="{{ route('mitra.register') }}" class="auth-link">Daftar sekarang</a>
                        </div>
                        <hr class="my-3">
                        <div class="text-center small text-muted">
                            Pelanggan? <a href="{{ route('login') }}" class="auth-link">Login Pelanggan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const input  = document.getElementById('wa_input');
    const hidden = document.getElementById('wa_hidden');

    if (hidden.value) {
        input.value = hidden.value.startsWith('62') ? hidden.value.slice(2) : hidden.value;
    }

    input.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
        hidden.value = this.value ? '62' + this.value : '';
    });

    document.getElementById('togglePwd').addEventListener('click', function () {
        const pwd = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            eye.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwd.type = 'password';
            eye.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
})();
</script>
@endsection
