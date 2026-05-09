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

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="no_wa" class="form-label">Nomor WhatsApp</label>
                    <input type="text" class="form-control @error('no_wa') is-invalid @enderror"
                           id="no_wa" name="no_wa" value="{{ old('no_wa') }}"
                           placeholder="Contoh: 81234567890" required autofocus>
                    @error('no_wa')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
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
@endsection
