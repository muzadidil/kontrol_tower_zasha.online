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
    .kategori-card {
        cursor: pointer; padding: 16px 12px; border: 2px solid #e9ecef; border-radius: 12px;
        text-align: center; transition: all 0.2s; background: #fff;
    }
    .kategori-card:hover { border-color: #005aa9; transform: translateY(-2px); }
    .btn-check:checked + .kategori-card { border-color: #005aa9; background: #f0f7ff; }
    .btn-check:checked + .kategori-card .kategori-icon {
        background: linear-gradient(135deg, #005aa9 0%, #003d75 100%); color: #fff;
    }
    .kategori-icon {
        width: 48px; height: 48px; background: #f0f7ff; color: #005aa9;
        border-radius: 50%; display: inline-flex; align-items: center;
        justify-content: center; font-size: 22px; margin-bottom: 8px;
    }
</style>

<div class="container auth-wrapper">
    <div class="row w-100 justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="auth-card row g-0">
                {{-- Hero --}}
                <div class="col-md-4 auth-hero d-none d-md-flex flex-column">
                    <div class="position-relative" style="z-index: 2;">
                        <span class="role-badge"><i class="bi bi-rocket-takeoff me-1"></i> JADI MITRA</span>
                        <h2 class="fw-bold mb-3">Mulai Berpenghasilan Bersama Zasha</h2>
                        <p class="opacity-75 mb-4">Pilih kategori jasa Anda, isi data dasar, dan mulai terima order hari ini.</p>

                        <div class="bg-white bg-opacity-10 rounded-3 p-3 small mb-3">
                            <strong class="d-block mb-1"><i class="bi bi-info-circle me-1"></i> Kategori Permanen</strong>
                            <span class="opacity-75">Setelah daftar, kategori tidak bisa diubah. Pilih sesuai keahlian utama Anda.</span>
                        </div>

                        <div class="bg-white bg-opacity-10 rounded-3 p-3 small">
                            <strong class="d-block mb-1"><i class="bi bi-clipboard-check me-1"></i> Setelah Daftar</strong>
                            <span class="opacity-75">Lengkapi dokumen verifikasi (KTP, foto profil, dll) untuk mulai aktif.</span>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <div class="col-md-8">
                    <div class="p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="{{ asset('images/logo_zasha.png') }}" alt="Zasha" style="height: 50px;" onerror="this.style.display='none'">
                            <h3 class="fw-bold mt-3 mb-1">Daftar Mitra</h3>
                            <p class="text-muted small">Pilih kategori dan isi data dasar Anda</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger small">
                                @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
                            </div>
                        @endif

                        <form action="{{ route('mitra.register.submit') }}" method="POST">@csrf

                            {{-- Kategori --}}
                            <label class="form-label small fw-semibold mb-2">Pilih Kategori Jasa <span class="text-danger">*</span></label>
                            <div class="row g-2 mb-4">
                                @foreach($kategoris as $k)
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="kategori_kode" id="kat-{{ $k['value'] }}"
                                        value="{{ $k['value'] }}" required {{ old('kategori_kode') === $k['value'] ? 'checked' : '' }}>
                                    <label for="kat-{{ $k['value'] }}" class="kategori-card d-block">
                                        <div class="kategori-icon">
                                            @if($k['value'] === 'TNG') <i class="bi bi-person-arms-up"></i>
                                            @elseif($k['value'] === 'WFH') <i class="bi bi-laptop"></i>
                                            @elseif($k['value'] === 'JST') <i class="bi bi-bag-check"></i>
                                            @elseif($k['value'] === 'SVC') <i class="bi bi-tools"></i>
                                            @endif
                                        </div>
                                        <div class="fw-bold small">{{ $k['label'] }}</div>
                                        <div class="text-muted" style="font-size: 11px;">{{ $k['value'] }}</div>
                                    </label>
                                </div>
                                @endforeach
                            </div>

                            {{-- Data Dasar --}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nama Lengkap (Sesuai KTP) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                        <input type="text" name="nama_asli" class="form-control"
                                            value="{{ old('nama_asli') }}" placeholder="Nama lengkap" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nama Panggilan <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-emoji-smile"></i></span>
                                        <input type="text" name="nama_panggilan" class="form-control"
                                            value="{{ old('nama_panggilan') }}" placeholder="Panggilan" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="tel" id="wa_input" inputmode="numeric" pattern="[0-9]*"
                                            class="form-control" placeholder="8xxxxxxxxxx" maxlength="13" required>
                                        <input type="hidden" name="nomor_wa" id="wa_hidden" value="{{ old('nomor_wa') }}">
                                    </div>
                                    <div class="form-text small">Pelanggan akan menghubungi via WA ini.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                        <input type="password" name="password" id="pwd1" class="form-control"
                                            placeholder="Min 6 karakter" minlength="6" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwd1','eye1')" tabindex="-1">
                                            <i class="bi bi-eye" id="eye1"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                        <input type="password" name="password_confirmation" id="pwd2" class="form-control"
                                            placeholder="Ulangi password" minlength="6" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePwd('pwd2','eye2')" tabindex="-1">
                                            <i class="bi bi-eye" id="eye2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-check mt-3 mb-4">
                                <input class="form-check-input" type="checkbox" id="agree" required>
                                <label class="form-check-label small" for="agree">
                                    Saya menyetujui <a href="#" class="auth-link">Syarat & Ketentuan Mitra</a> Zasha
                                </label>
                            </div>

                            <button type="submit" class="btn btn-mitra w-100">
                                <i class="bi bi-rocket-takeoff me-1"></i> Daftar & Mulai
                            </button>
                        </form>

                        <div class="text-center mt-4 small">
                            Sudah jadi mitra? <a href="{{ route('mitra.login') }}" class="auth-link">Login di sini</a>
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
