@extends('layouts.guest')

@section('content')
<style>
    :root {
        --blue-deep:   #002d72;
        --blue-mid:    #0047b3;
        --blue-light:  #3b82f6;
        --blue-pale:   #dbeafe;
        --gold:        #f0a500;
        --gold-light:  #fef3c7;
        --bg:          #eef2fb;
    }

    body { background: var(--bg); min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
    .auth-wrapper { min-height: 100vh; display: flex; align-items: center; padding: 30px 0; }
    .auth-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 45, 114, 0.12);
        overflow: hidden;
    }
    .auth-hero {
        background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-mid) 100%);
        color: #fff; padding: 50px 40px; position: relative; overflow: hidden;
    }
    .auth-hero::before {
        content: ''; position: absolute; top: -50px; right: -50px;
        width: 200px; height: 200px; background: rgba(255,255,255,0.08); border-radius: 50%;
    }
    .auth-hero::after {
        content: ''; position: absolute; bottom: -80px; left: -80px;
        width: 250px; height: 250px; background: rgba(255,255,255,0.06); border-radius: 50%;
    }
    .feature-list li { padding: 8px 0; list-style: none; font-size: 14px; }
    .feature-list i {
        background: var(--gold); width: 28px; height: 28px;
        border-radius: 50%; display: inline-flex; align-items: center;
        justify-content: center; margin-right: 10px; color: var(--blue-deep);
    }
    .form-control:focus { border-color: var(--blue-mid); box-shadow: 0 0 0 0.2rem rgba(0, 71, 179, 0.15); }
    .btn-zasha {
        background: var(--blue-deep);
        border: none; color: #fff; font-weight: 700; padding: 13px;
        border-radius: 12px; transition: all 0.3s;
    }
    .btn-zasha:hover { background: var(--blue-mid); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 45, 114, 0.25); color: #fff; }
    .input-group-text { background: var(--blue-pale); border-color: var(--blue-pale); font-weight: 700; color: var(--blue-deep); }
    .auth-link { color: var(--blue-deep); font-weight: 600; text-decoration: none; }
    .auth-link:hover { color: var(--blue-mid); text-decoration: underline; }
    .role-badge {
        display: inline-flex; align-items: center; background: var(--gold);
        color: var(--blue-deep); padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-bottom: 16px;
    }
</style>

<div class="container auth-wrapper">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="auth-card row g-0">
                {{-- Hero (Left) --}}
                <div class="col-md-5 auth-hero d-none d-md-flex flex-column">
                    <div class="position-relative" style="z-index: 2;">
                        <span class="role-badge"><i class="bi bi-person-fill me-1"></i> PELANGGAN</span>
                        <h2 class="fw-bold mb-3">Selamat Datang Kembali!</h2>
                        <p class="opacity-75 mb-4">Akses jasa profesional, top-up, PPOB, dan banyak lagi dalam satu aplikasi.</p>
                        <ul class="feature-list ps-0 mb-0">
                            <li><i class="bi bi-check-lg"></i> Order WFH, Jastip, Tenaga, Service</li>
                            <li><i class="bi bi-check-lg"></i> Pulsa, Paket Data, Token Listrik</li>
                            <li><i class="bi bi-check-lg"></i> Top-Up Game & Voucher</li>
                            <li><i class="bi bi-check-lg"></i> Tracking pesanan real-time</li>
                        </ul>
                    </div>
                </div>

                {{-- Form (Right) --}}
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logo_zasha.png') }}" alt="Zasha" style="height: 60px;" onerror="this.style.display='none'">
                            <h3 class="fw-bold mt-3 mb-1">Login Pelanggan</h3>
                            <p class="text-muted small">Masuk untuk akses semua layanan</p>
                        </div>

                        @if(session('error')) <div class="alert alert-danger small">{{ session('error') }}</div> @endif
                        @if(session('success')) <div class="alert alert-success small">{{ session('success') }}</div> @endif

                        <form action="{{ route('login.submit') }}" method="POST" id="loginForm">@csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="tel" class="form-control @error('no_wa') is-invalid @enderror"
                                        id="no_wa_input" inputmode="numeric" pattern="[0-9]*"
                                        placeholder="8xxxxxxxxxx" maxlength="13" autofocus>
                                    <input type="hidden" name="no_wa" id="no_wa_hidden" value="{{ old('no_wa') }}">
                                </div>
                                @error('no_wa') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required placeholder="••••••••">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1">
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember')?'checked':'' }}>
                                    <label class="form-check-label small" for="remember">Ingat saya</label>
                                </div>
                                <a href="#" class="auth-link small">Lupa password?</a>
                            </div>

                            <button type="submit" class="btn btn-zasha w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </button>
                        </form>

                        <div class="text-center mt-4 small">
                            Belum punya akun?
                            <a href="{{ route('register') }}" class="auth-link">Daftar di sini</a>
                        </div>
                        <hr class="my-3">
                        <div class="text-center small text-muted">
                            Mitra? <a href="{{ route('mitra.login') }}" class="auth-link">Login Mitra</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const input  = document.getElementById('no_wa_input');
    const hidden = document.getElementById('no_wa_hidden');

    if (hidden.value) {
        input.value = hidden.value.startsWith('62') ? hidden.value.slice(2) : hidden.value;
    }

    input.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
        hidden.value = this.value ? '62' + this.value : '';
    });

    const toggleBtn = document.getElementById('togglePwd');
    const eyeIcon   = document.getElementById('eyeIcon');
    const pwdInput  = document.getElementById('password');

    toggleBtn.addEventListener('click', function () {
        if (pwdInput.type === 'password') {
            pwdInput.type = 'text';
            eyeIcon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            pwdInput.type = 'password';
            eyeIcon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
})();
</script>
@endsection
