@extends('layouts.guest')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo_zasha.png') }}" alt="Logo Zasha" style="width: 150px; height: auto;">
        <h2 class="mt-3 text-dark">Login Mitra</h2>
        <p class="text-muted small">Masuk ke akun mitra Anda</p>
    </div>

    <div class="card card-zasha p-4" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger small py-2">{{ session('error') }}</div>
            @endif

            <form action="{{ route('mitra.login.submit') }}" method="POST" id="mitraLoginForm">
                @csrf

                {{-- Nomor WhatsApp --}}
                <div class="mb-3">
                    <label for="nomor_wa_input" class="form-label">Nomor WhatsApp</label>
                    <div class="input-group @error('nomor_wa') is-invalid @enderror">
                        <span class="input-group-text bg-light text-muted fw-bold" style="user-select:none;">+62</span>
                        <input type="text" class="form-control @error('nomor_wa') is-invalid @enderror"
                               id="nomor_wa_input" inputmode="numeric" pattern="[0-9]*"
                               placeholder="8xxxxxxxxxx" maxlength="11" autofocus>
                        <input type="hidden" name="nomor_wa" id="nomor_wa_hidden" value="{{ old('nomor_wa') }}">
                    </div>
                    <div id="wa-feedback" class="form-text text-danger small d-none"></div>
                    @error('nomor_wa')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Ingat Saya</label>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Login</button>
            </form>

            <div class="text-center mt-3">
                <small class="text-muted">Login sebagai pelanggan? <a href="{{ route('login') }}">Klik di sini</a></small>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const input    = document.getElementById('nomor_wa_input');
    const hidden   = document.getElementById('nomor_wa_hidden');
    const feedback = document.getElementById('wa-feedback');

    const oldVal = hidden.value;
    if (oldVal) {
        input.value = oldVal.startsWith('62') ? oldVal.slice(2) : oldVal;
    }

    function validate(val) {
        if (val.length > 0 && val[0] !== '8') return 'Nomor harus diawali angka <strong>8</strong>';
        if (val.length > 0 && val.length !== 11) return 'Nomor harus <strong>11 digit</strong> setelah +62';
        return null;
    }

    input.addEventListener('input', function () {
        this.value  = this.value.replace(/\D/g, '');
        const val   = this.value;
        const err   = validate(val);
        if (err) {
            feedback.innerHTML = err;
            feedback.classList.remove('d-none');
            input.classList.add('is-invalid');
        } else {
            feedback.classList.add('d-none');
            input.classList.remove('is-invalid');
        }
        hidden.value = val ? '62' + val : '';
    });

    document.getElementById('mitraLoginForm').addEventListener('submit', function (e) {
        const val = input.value;
        const err = validate(val);
        if (err) {
            e.preventDefault();
            feedback.innerHTML = err;
            feedback.classList.remove('d-none');
            input.classList.add('is-invalid');
            input.focus();
        }
    });

    // Toggle password
    const toggleBtn = document.getElementById('togglePassword');
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
