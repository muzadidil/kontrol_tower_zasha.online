@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .header-profile { background: var(--zasha-blue); color: white; padding: 20px; text-align: center; }
    .avatar-wrapper { position: relative; display: inline-block; margin-top: -50px; }
    .profile-img-big { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
    .form-label-custom { font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .address-item { border-radius: 15px; border: 1px solid #e2e8f0; }
</style>

<div class="header-profile" style="background: linear-gradient(135deg, var(--zasha-blue), #0047b3); padding: 40px 20px 60px; text-align:center;">
    <h5 class="fw-bold m-0 text-white">Akun Saya</h5>
</div>

<div class="container text-center px-4">
    <div class="avatar-wrapper mb-2">
        <img src="{{ $user->foto ? asset('storage/'.$user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->nama_pelanggan).'&background=002d72&color=fff' }}"
             class="profile-img-big">
    </div>

    <div class="mt-1 mb-1">
        @if($user->status_verifikasi == 1)
            <span class="badge bg-success">TERVERIFIKASI</span>
        @else
            <span class="badge bg-danger">BELUM TERVERIFIKASI</span>
        @endif
    </div>

    <h5 class="fw-bold mt-1">{{ $user->nama_pelanggan }}</h5>
    <p class="text-muted small">Saldo: <strong class="text-primary">Rp {{ number_format($user->saldo ?? 0, 0, ',', '.') }}</strong></p>

    @if(session('success'))
        <div class="alert alert-success rounded-3 small py-2">{{ session('success') }}</div>
    @endif

    {{-- Form Data Pribadi --}}
    <div class="card card-custom p-4 text-start mt-2 mb-3">
        <h6 class="fw-bold small text-muted text-uppercase mb-3">Data Pribadi</h6>
        <form action="{{ route('pelanggan.profil.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">Nama Lengkap</label>
                <input type="text" name="nama_pelanggan" class="form-control rounded-3"
                       value="{{ old('nama_pelanggan', $user->nama_pelanggan) }}">
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Nomor WhatsApp</label>
                <div class="input-group">
                    <span class="input-group-text rounded-start-3" style="font-size:0.85rem;">+62</span>
                    <input type="number" name="no_wa" class="form-control rounded-end-3"
                           value="{{ old('no_wa', ltrim($user->no_wa ?? '', '62')) }}">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">SIMPAN PERUBAHAN</button>
        </form>
    </div>

    {{-- Logout --}}
    <div class="text-start mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100 rounded-pill fw-bold">
                <i class="bi bi-box-arrow-right me-2"></i>Keluar
            </button>
        </form>
    </div>

    {{-- Buku Alamat --}}
    <div class="text-start mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold small text-muted text-uppercase mb-0">Buku Alamat</h6>
            <a href="{{ route('pelanggan.alamat') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size:0.7rem;">
                <i class="bi bi-plus me-1"></i>Kelola
            </a>
        </div>
        @forelse($alamats as $a)
            <div class="address-item bg-white p-3 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-primary mb-1">{{ $a->label_alamat }}</span>
                        @if($a->is_utama)
                            <span class="badge bg-warning text-dark mb-1 ms-1">Utama</span>
                        @endif
                        <p class="small mb-1 fw-bold">{{ $a->nama_penerima }} &middot; {{ $a->no_wa_penerima }}</p>
                        <p class="small mb-0 text-muted">{{ $a->alamat_lengkap }}</p>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-3 text-muted small">
                <i class="bi bi-geo-alt fs-4 d-block mb-1"></i>Belum ada alamat tersimpan.
                <a href="{{ route('pelanggan.alamat') }}" class="d-block mt-2 text-primary fw-bold">+ Tambah Alamat</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
