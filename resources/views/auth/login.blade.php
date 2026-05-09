@extends('layouts.guest')

@section('content')
<div class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo_zasha.png') }}" alt="Logo Zasha" style="width: 150px; height: auto;">
        <h2 class="mt-3 text-dark">Login Pelanggan</h2>
    </div>

    <div class="card card-zasha p-4" style="max-width: 400px; width: 100%;">
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger small py-2">{{ session('error') }}</div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                @csrf

                {{-- No WhatsApp --}}
                <div class="mb-3">
                    <label for="no_wa_input" class="form-label">Nomor WhatsApp</label>
                    <div class="input-group @error('no_wa') is-invalid @enderror">
                        <span class="input-group-text bg-light text-muted fw-bold" style="user-select:none;">+62</span>
                        <input type="text" class="form-control @error('no_wa') is-invalid @enderror"
                               id="no_wa_input" inputmode="numeric" pattern="[0-9]*"
                               placeholder="8xxxxxxxxxx" maxlength="11" autocomplete="tel" autofocus>
                        <input type="hidden" name="no_wa" id="no_wa_hidden" value="{{ old('no_wa') }}">
                    </div>
                    <div id="wa-feedback" class="form-text text-danger small d-none" id="wa-feedback"></div>
                    @error('no_wa')
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
        </div>
    </div>
</div>

<script>
(function () {
    const input     = document.getElementById('no_wa_input');
    const hidden    = document.getElementById('no_wa_hidden');
    const feedback  = document.getElementById('wa-feedback');

    // Isi ulang input saat ada old value (setelah error dari server)
    const oldVal = hidden.value;
    if (oldVal) {
        // Hapus awalan 62 jika ada, supaya tampil hanya bagian setelah +62
        input.value = oldVal.startsWith('62') ? oldVal.slice(2) : oldVal;
    }

    function validate(val) {
        if (val.length > 0 && val[0] !== '8') {
            return 'Nomor harus diawali angka <strong>8</strong> (contoh: 81234567890)';
        }
        if (val.length > 0 && val.length !== 11) {
            return 'Nomor harus <strong>11 digit</strong> setelah +62 (contoh: 81234567890)';
        }
        return null;
    }

    input.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
        const val  = this.value;
        const err  = validate(val);

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

    // Validasi sebelum submit
    document.getElementById('loginForm').addEventListener('submit', function (e) {
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

    // Toggle password visibility
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
