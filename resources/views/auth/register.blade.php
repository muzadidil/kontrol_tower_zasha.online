@extends('layouts.guest')

@section('content')
<style>
    body { background: linear-gradient(135deg, #fff8e7 0%, #ffe5b4 100%); min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
    .auth-wrapper { min-height: 100vh; display: flex; align-items: center; padding: 30px 0; }
    .auth-card { background: #fff; border-radius: 24px; box-shadow: 0 20px 60px rgba(240,165,0,0.15); overflow: hidden; }
    .auth-hero {
        background: linear-gradient(135deg, #f0a500 0%, #ff8c00 100%);
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
    .form-control:focus { border-color: #f0a500; box-shadow: 0 0 0 0.2rem rgba(240,165,0,0.15); }
    .btn-zasha {
        background: linear-gradient(135deg, #f0a500 0%, #ff8c00 100%);
        border: none; color: #fff; font-weight: 700; padding: 13px;
        border-radius: 12px; transition: all 0.3s;
    }
    .btn-zasha:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(240,165,0,0.3); color: #fff; }
    .input-group-text { background: #fffaf0; border-color: #ffe5b4; font-weight: 700; color: #f0a500; }
    .auth-link { color: #f0a500; font-weight: 600; text-decoration: none; }
    .auth-link:hover { color: #ff8c00; text-decoration: underline; }
    .role-badge {
        display: inline-flex; align-items: center; background: rgba(255,255,255,0.2);
        padding: 6px 14px; border-radius: 50px; font-size: 12px; font-weight: 600; margin-bottom: 16px;
    }
    .step-icon {
        width: 36px; height: 36px; background: rgba(255,255,255,0.2); border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center; margin-right: 12px;
    }
</style>

<div class="container auth-wrapper">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="auth-card row g-0">
                {{-- Hero (Left) --}}
                <div class="col-md-5 auth-hero d-none d-md-flex flex-column">
                    <div class="position-relative" style="z-index: 2;">
                        <span class="role-badge"><i class="bi bi-person-plus-fill me-1"></i> DAFTAR</span>
                        <h2 class="fw-bold mb-3">Bergabung dengan Zasha</h2>
                        <p class="opacity-75 mb-4">Ratusan mitra siap melayani kebutuhan Anda. Daftar dan nikmati layanan dalam hitungan menit.</p>

                        <div class="d-flex align-items-start mb-3">
                            <div class="step-icon"><i class="bi bi-1-circle-fill"></i></div>
                            <div>
                                <strong class="d-block">Daftar Singkat</strong>
                                <small class="opacity-75">Cukup nama & nomor WhatsApp</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="step-icon"><i class="bi bi-2-circle-fill"></i></div>
                            <div>
                                <strong class="d-block">Lengkapi Profil</strong>
                                <small class="opacity-75">Tambah alamat & data diri</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="step-icon"><i class="bi bi-3-circle-fill"></i></div>
                            <div>
                                <strong class="d-block">Mulai Pesan</strong>
                                <small class="opacity-75">Pilih jasa atau top-up favorit</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form (Right) --}}
                <div class="col-md-7">
                    <div class="p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logo_zasha.png') }}" alt="Zasha" style="height: 50px;" onerror="this.style.display='none'">
                            <h3 class="fw-bold mt-3 mb-1">Daftar Akun</h3>
                            <p class="text-muted small">Hanya butuh 30 detik</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger small">
                                @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
                            </div>
                        @endif

                        <form action="{{ route('register.submit') }}" method="POST" id="regForm">@csrf

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" name="nama_pelanggan" class="form-control"
                                        value="{{ old('nama_pelanggan') }}" placeholder="Nama lengkap Anda" required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Nomor WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="tel" id="no_wa_input" inputmode="numeric" pattern="[0-9]*"
                                        class="form-control" placeholder="8xxxxxxxxxx" maxlength="13" required>
                                    <input type="hidden" name="no_wa" id="no_wa_hidden" value="{{ old('no_wa') }}">
                                </div>
                                <div class="form-text small">Pastikan nomor aktif untuk menerima notifikasi.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control"
                                        placeholder="Min 6 karakter" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password', 'eye1')" tabindex="-1">
                                        <i class="bi bi-eye" id="eye1"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Konfirmasi Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirm"
                                        class="form-control" placeholder="Ulangi password" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('password_confirm', 'eye2')" tabindex="-1">
                                        <i class="bi bi-eye" id="eye2"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="agree" required>
                                <label class="form-check-label small" for="agree">
                                    Saya setuju dengan <a href="#" class="auth-link">Syarat & Ketentuan</a> Zasha
                                </label>
                            </div>

                            <button type="submit" class="btn btn-zasha w-100">
                                <i class="bi bi-person-plus-fill me-1"></i> Daftar Sekarang
                            </button>
                        </form>

                        <div class="text-center mt-4 small">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="auth-link">Login di sini</a>
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

    window.togglePwd = function (id, eyeId) {
        const el = document.getElementById(id);
        const eye = document.getElementById(eyeId);
        if (el.type === 'password') {
            el.type = 'text';
            eye.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            el.type = 'password';
            eye.classList.replace('bi-eye-slash', 'bi-eye');
        }
    };
})();
</script>
@endsection
