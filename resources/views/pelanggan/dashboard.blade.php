@extends('layouts.pelanggan')

@section('content')
<div class="container py-3">
    
    <div class="d-flex justify-content-between align-items-center mb-4 pt-2">
        <div class="d-flex align-items-center">
            <img src="{{ !empty($user->foto) ? $user->foto : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=002d72&color=fff' }}" class="profile-img shadow-sm me-3">
            <div>
                <h6 class="fw-bold mb-0 allow-select">Halo, {{ explode(' ', trim($user->name))[0] }}!</h6>
                <span class="id-badge shadow-sm allow-select">{{ $user->kode_zasha }}</span>
            </div>
        </div>
        <a href="{{ route('pelanggan.notifikasi') }}" class="btn btn-white shadow-sm rounded-circle p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white;">
            <i class="bi bi-bell fs-5 text-dark"></i>
        </a>
    </div>

    @if($user->is_verif == 0)
    <div class="card card-custom mb-4 bg-white border-start border-danger border-4 p-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-shield-lock-fill text-danger fs-2 me-3"></i>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-danger mb-1" style="font-size: 0.8rem;">Verifikasi Akun</h6>
                <p class="text-muted mb-0" style="font-size: 0.65rem;">Lengkapi profil untuk memesan.</p>
            </div>
            <a href="{{ route('pelanggan.profil') }}" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" style="font-size: 0.65rem;">LENGKAPI</a>
        </div>
    </div>
    @endif

    <div class="card card-custom p-3 bg-white border-start border-primary border-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted fw-bold d-block mb-1" style="font-size: 10px;">DOMPET ZASHA</small>
                <h5 class="fw-bold mb-0 text-primary allow-select">Rp {{ number_format($user->saldo, 0, ',', '.') }}</h5>
            </div>
            <a href="{{ route('pelanggan.topup') }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="bi bi-plus-lg me-1"></i>Isi Saldo
            </a>
        </div>
    </div>

    <h6 class="fw-bold mb-3 small text-muted text-uppercase" style="letter-spacing: 0.5px;">Layanan Zasha</h6>
    <div class="row row-cols-4 g-2 mb-4 text-center">
        @foreach($categories as $kat)
            @php
                $locked = ($user->is_verif == 0);
            @endphp
            <div class="col">
                <a href="{{ $locked ? '#' : route('pelanggan.katalog', ['id_kategori' => $kat->id_kategori]) }}" 
                   class="btn-menu {{ $locked ? 'locked' : '' }}" 
                   {!! $locked ? "onclick='bukaModalLocked()'" : "" !!} 
                   style="cursor:pointer;">
                    <div class="svg-icon">
                        @if($locked)
                            <i class="bi bi-lock-fill text-muted"></i>
                        @else
                            {!! $kat->svg_kategori !!}
                        @endif
                    </div>
                    <span>{{ $kat->nama_kategori }}</span>
                </a>
            </div>
        @endforeach
    </div>
    
    <div class="card card-custom bg-primary text-white p-3 border-0 mt-2" style="background: linear-gradient(135deg, #002d72, #004a9f);">
        <div class="d-flex align-items-center">
            <i class="bi bi-rocket-takeoff-fill fs-2 text-warning me-3"></i>
            <div>
                <h6 class="fw-bold mb-1 small">Siap Antar Jemput</h6>
                <p class="mb-0 text-white-50" style="font-size: 0.65rem;">Layanan jastip andalan warga Jember.</p>
            </div>
        </div>
    </div>
</div>
@endsection