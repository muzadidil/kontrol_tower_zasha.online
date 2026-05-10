@extends('layouts.pelanggan')

@section('content')

{{-- ── HERO PROFIL ──────────────────────────────── --}}
<div style="background:linear-gradient(145deg,#001d4d,#002d72,#0047b3);
            padding:34px 21px 55px; text-align:center; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-21px;right:-21px;width:89px;height:89px;
                border-radius:50%;background:rgba(240,165,0,.12);"></div>
    <div style="position:absolute;bottom:-13px;left:-13px;width:55px;height:55px;
                border-radius:50%;background:rgba(255,255,255,.07);"></div>

    <div style="font-size:16px;font-weight:800;color:white;margin-bottom:21px;">Akun Saya</div>

    {{-- Avatar --}}
    <div style="position:relative;display:inline-block;margin-bottom:13px;">
        <img src="{{ $user->foto
                ? asset('storage/'.$user->foto)
                : 'https://ui-avatars.com/api/?name='.urlencode($user->nama_pelanggan).'&background=f0a500&color=002d72&bold=true&size=128' }}"
             style="width:88px;height:88px;border-radius:21px;object-fit:cover;
                    border:3px solid rgba(240,165,0,.6);box-shadow:0 8px 24px rgba(0,0,0,.2);">
        <div style="position:absolute;bottom:-5px;right:-5px;width:21px;height:21px;
                    border-radius:50%;display:flex;align-items:center;justify-content:center;
                    {{ $user->status_verifikasi == 1 ? 'background:#10b981;' : 'background:#ef4444;' }}
                    border:2px solid #002d72;">
            <i class="bi {{ $user->status_verifikasi == 1 ? 'bi-check-lg' : 'bi-x-lg' }}"
               style="color:white;font-size:10px;"></i>
        </div>
    </div>

    <div style="font-size:19px;font-weight:800;color:white;margin-bottom:5px;">{{ $user->nama_pelanggan }}</div>
    <div style="font-size:11px;color:rgba(255,255,255,.55);">
        {{ $user->status_verifikasi == 1 ? 'Akun Terverifikasi' : 'Belum Terverifikasi' }}
    </div>
</div>

{{-- ── KONTEN ───────────────────────────────────── --}}
<div style="padding:0 21px;margin-top:-34px;">

    @if(session('success'))
        <div style="background:#d1fae5;border-radius:13px;padding:13px 16px;margin-bottom:13px;
                    display:flex;gap:10px;align-items:center;">
            <i class="bi bi-check-circle-fill" style="color:#10b981;font-size:18px;"></i>
            <span style="font-size:12px;color:#065f46;font-weight:600;">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Saldo chip --}}
    <div class="z-card" style="padding:13px 21px;margin-bottom:13px;display:flex;
                               align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;
                        display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-wallet2" style="color:#002d72;font-size:16px;"></i>
            </div>
            <div>
                <div style="font-size:9px;font-weight:700;color:var(--text-faint);text-transform:uppercase;">Saldo</div>
                <div class="allow-select" style="font-size:15px;font-weight:800;color:#002d72;">
                    Rp {{ number_format($user->saldo ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <a href="{{ route('pelanggan.dompet') }}"
           style="background:var(--gold);color:#002d72;border-radius:34px;padding:7px 16px;
                  font-size:11px;font-weight:800;text-decoration:none;">Isi Saldo</a>
    </div>

    {{-- Form Data Pribadi --}}
    <div class="z-card" style="padding:21px;margin-bottom:13px;">
        <div style="font-size:10px;font-weight:800;color:var(--text-faint);text-transform:uppercase;
                    letter-spacing:.5px;margin-bottom:21px;display:flex;align-items:center;gap:8px;">
            <i class="bi bi-person-lines-fill" style="color:#002d72;font-size:13px;"></i>
            Data Pribadi
        </div>
        <form action="{{ route('pelanggan.profil.update') }}" method="POST">
            @csrf
            <div style="margin-bottom:13px;">
                <div style="font-size:10px;font-weight:700;color:var(--text-faint);
                            text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Nama Lengkap</div>
                <input type="text" name="nama_pelanggan" class="form-control allow-select"
                       value="{{ old('nama_pelanggan', $user->nama_pelanggan) }}"
                       style="border-radius:13px;border-color:var(--border);font-size:14px;
                              font-weight:600;padding:12px 16px;">
            </div>
            <div style="margin-bottom:21px;">
                <div style="font-size:10px;font-weight:700;color:var(--text-faint);
                            text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Nomor WhatsApp</div>
                <div class="input-group">
                    <span class="input-group-text"
                          style="border-radius:13px 0 0 13px;border-color:var(--border);
                                 font-size:13px;font-weight:700;color:#002d72;background:#eff6ff;">+62</span>
                    <input type="number" name="no_wa" class="form-control allow-select"
                           value="{{ old('no_wa', ltrim($user->no_wa ?? '', '62')) }}"
                           style="border-radius:0 13px 13px 0;border-color:var(--border);
                                  font-size:14px;font-weight:600;padding:12px 16px;">
                </div>
            </div>
            <button type="submit" class="btn-z-primary w-100" style="padding:13px;">SIMPAN PERUBAHAN</button>
        </form>
    </div>

    {{-- Buku Alamat --}}
    <div class="z-card" style="padding:21px;margin-bottom:13px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:21px;">
            <div style="font-size:10px;font-weight:800;color:var(--text-faint);text-transform:uppercase;
                        letter-spacing:.5px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-geo-alt-fill" style="color:#002d72;font-size:13px;"></i>Buku Alamat
            </div>
            <a href="{{ route('pelanggan.alamat') }}"
               style="background:#eff6ff;color:#002d72;border-radius:34px;padding:5px 13px;
                      font-size:10px;font-weight:800;text-decoration:none;">
                <i class="bi bi-pencil-fill me-1" style="font-size:9px;"></i>Kelola
            </a>
        </div>
        @forelse($alamats as $a)
            <div style="border-radius:13px;border:1px solid var(--border);padding:13px;
                        margin-bottom:8px;background:#f8fafc;">
                <div style="display:flex;gap:8px;margin-bottom:5px;">
                    <span style="background:#eff6ff;color:#002d72;border-radius:34px;font-size:9px;
                                 font-weight:800;padding:3px 10px;text-transform:uppercase;">{{ $a->label_alamat }}</span>
                    @if($a->is_utama)
                        <span style="background:var(--gold-light);color:var(--gold-dark);border-radius:34px;
                                     font-size:9px;font-weight:800;padding:3px 10px;">Utama</span>
                    @endif
                </div>
                <div style="font-size:13px;font-weight:700;color:var(--text-main);">
                    {{ $a->nama_penerima }} &middot; {{ $a->no_wa_penerima }}
                </div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">{{ $a->alamat_lengkap }}</div>
            </div>
        @empty
            <div style="text-align:center;padding:21px 0;color:var(--text-faint);">
                <i class="bi bi-geo-alt" style="font-size:34px;display:block;margin-bottom:8px;opacity:.4;"></i>
                <div style="font-size:12px;font-weight:600;">Belum ada alamat tersimpan</div>
                <a href="{{ route('pelanggan.alamat') }}"
                   style="font-size:12px;font-weight:800;color:#002d72;text-decoration:none;
                          display:inline-block;margin-top:8px;">+ Tambah Alamat</a>
            </div>
        @endforelse
    </div>

    {{-- Logout --}}
    <form action="{{ route('logout') }}" method="POST" style="margin-bottom:34px;">
        @csrf
        <button type="submit"
                style="width:100%;background:transparent;color:#ef4444;border:1.5px solid #fca5a5;
                       border-radius:34px;padding:13px;font-size:13px;font-weight:800;
                       display:flex;align-items:center;justify-content:center;gap:8px;">
            <i class="bi bi-box-arrow-right"></i>Keluar dari Akun
        </button>
    </form>
</div>
@endsection
