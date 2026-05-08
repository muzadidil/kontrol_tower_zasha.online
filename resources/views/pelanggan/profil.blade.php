<div class="header-profile">
    <h5 class="fw-bold m-0 text-white">Akun Saya</h5>
</div>

<div class="container text-center px-4">
    <!-- Foto Profil Dinamis -->
    <div class="avatar-wrapper">
        <img src="{{ $user->foto ? asset('storage/'.$user->foto) : 'https://ui-avatars.com/api/?name='.urlencode($user->nama_pelanggan) }}" class="profile-img-big">
    </div>

    <!-- Status Verifikasi Otomatis -->
    <div class="mt-2">
        @if($user->status_verifikasi == 1)
            <span class="badge bg-success">TERVERIFIKASI</span>
        @else
            <span class="badge bg-danger">LENGKAPI DATA</span>
        @endif
    </div>

    <h5 class="fw-bold mt-2">{{ $user->nama_pelanggan }}</h5>
    <p class="text-muted small">Saldo: <strong>Rp {{ number_format($user->saldo, 0, ',', '.') }}</strong></p>

    <!-- Form Data Pribadi -->
    <div class="card card-custom p-4 text-start mt-3">
        <form action="{{ route('pelanggan.profil.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label-custom">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="{{ $user->nama_pelanggan }}">
            </div>
            <div class="mb-3">
                <label class="form-label-custom">Nomor WhatsApp</label>
                <input type="number" name="no_wa" class="form-control" value="{{ substr($user->no_wa, 2) }}">
            </div>
            <button type="submit" class="btn btn-primary w-100 rounded-pill">SIMPAN PERUBAHAN</button>
        </form>
    </div>

    <!-- Buku Alamat Dinamis -->
    <div class="text-start mt-4">
        <h6 class="fw-bold small text-muted text-uppercase">Buku Alamat</h6>
        @forelse($alamats as $a)
            <div class="address-item shadow-sm p-3 mb-2 bg-light rounded">
                <span class="badge bg-primary mb-1">{{ $a->label_alamat }}</span>
                <p class="small mb-0">{{ $a->alamat_lengkap }}</p>
                <small class="text-muted">Patokan: {{ $a->patokan }}</small>
            </div>
        @empty
            <div class="alert alert-light border-dashed">Belum ada alamat tersimpan.</div>
        @endforelse
    </div>
</div>