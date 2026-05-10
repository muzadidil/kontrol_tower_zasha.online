@extends('layouts.pelanggan')

@section('content')

{{-- ── HERO HEADER ──────────────────────────────── --}}
<div style="background: linear-gradient(145deg, #002d72 0%, #0047b3 60%, #005dd6 100%);
            padding: 21px 21px 55px; position: relative; overflow: hidden;">

    {{-- decorative circles (golden ratio proportions) --}}
    <div style="position:absolute; top:-34px; right:-34px; width:144px; height:144px;
                border-radius:50%; background:rgba(240,165,0,.12);"></div>
    <div style="position:absolute; bottom:-21px; left:-13px; width:89px; height:89px;
                border-radius:50%; background:rgba(255,255,255,.06);"></div>

    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ !empty($user->foto)
                    ? $user->foto
                    : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=f0a500&color=002d72&bold=true' }}"
                 style="width:44px;height:44px;border-radius:13px;object-fit:cover;
                        border:2.5px solid rgba(240,165,0,.6); flex-shrink:0;">
            <div>
                <div style="font-size:11px; color:rgba(255,255,255,.65); font-weight:600;">Selamat datang,</div>
                <div class="allow-select" style="font-size:18px; font-weight:800; color:#fff; line-height:1.2;">
                    {{ explode(' ', trim($user->name))[0] }} 👋
                </div>
            </div>
        </div>
        <a href="{{ route('pelanggan.notifikasi') }}"
           style="width:42px;height:42px;border-radius:13px;background:rgba(255,255,255,.12);
                  display:flex;align-items:center;justify-content:center;
                  border:1px solid rgba(255,255,255,.2); position:relative; flex-shrink:0; text-decoration:none;">
            <i class="bi bi-bell-fill" style="color:white;font-size:18px;"></i>
            @if(!empty($unread_notif) && $unread_notif > 0)
                <span style="position:absolute;top:-5px;right:-5px;background:#f0a500;
                             color:#002d72;font-size:8px;font-weight:800;width:18px;height:18px;
                             border-radius:50%;display:flex;align-items:center;justify-content:center;
                             border:2px solid #0047b3;">
                    {{ $unread_notif > 9 ? '9+' : $unread_notif }}
                </span>
            @endif
        </a>
    </div>

    {{-- Kode Zasha badge --}}
    <div class="allow-select mt-2" style="display:inline-flex;align-items:center;gap:6px;
              background:rgba(240,165,0,.18);border-radius:34px;padding:4px 13px;
              border:1px solid rgba(240,165,0,.35);">
        <i class="bi bi-shield-check" style="color:#f0a500;font-size:11px;"></i>
        <span style="font-size:10px;font-weight:800;color:#f0a500;letter-spacing:.5px;">{{ $user->kode_zasha }}</span>
    </div>
</div>

{{-- ── SALDO CARD (float up) ──────────────────── --}}
<div style="padding: 0 21px; margin-top: -34px;">

    @if($user->is_verif == 0)
    <div style="background:#fff3cd; border-radius:13px; padding:13px 16px; margin-bottom:13px;
                display:flex; align-items:center; gap:13px; border:1px solid #ffc107;">
        <i class="bi bi-exclamation-triangle-fill" style="color:#b45309;font-size:21px;flex-shrink:0;"></i>
        <div style="flex:1;">
            <div style="font-size:12px;font-weight:800;color:#92400e;">Akun Belum Terverifikasi</div>
            <div style="font-size:10px;color:#78350f;">Lengkapi profil untuk bisa memesan.</div>
        </div>
        <a href="{{ route('pelanggan.profil') }}"
           style="background:#f0a500;color:#002d72;border-radius:34px;padding:6px 13px;
                  font-size:10px;font-weight:800;text-decoration:none;white-space:nowrap;">LENGKAPI</a>
    </div>
    @endif

    <div class="z-card" style="padding:21px; background:linear-gradient(135deg,#001d4d,#002d72);
                               position:relative; overflow:hidden;">
        <div style="position:absolute;top:-13px;right:-13px;width:89px;height:89px;
                    border-radius:50%;background:rgba(240,165,0,.1);"></div>
        <div style="position:absolute;bottom:-21px;right:34px;width:55px;height:55px;
                    border-radius:50%;background:rgba(255,255,255,.05);"></div>

        <div style="font-size:10px;font-weight:700;color:rgba(255,255,255,.55);letter-spacing:.5px;
                    text-transform:uppercase;margin-bottom:8px;">
            <i class="bi bi-wallet2 me-1"></i>Dompet Zasha
        </div>
        <div class="allow-select" style="font-size:28px;font-weight:800;color:#fff;letter-spacing:-.5px;
                    margin-bottom:21px; line-height:1;">
            Rp {{ number_format($user->saldo, 0, ',', '.') }}
        </div>
        <a href="{{ route('pelanggan.dompet') }}"
           style="background:var(--gold);color:#002d72;border-radius:34px;padding:10px 21px;
                  font-size:12px;font-weight:800;text-decoration:none;display:inline-flex;
                  align-items:center;gap:6px;">
            <i class="bi bi-plus-circle-fill"></i>Isi Saldo
        </a>
    </div>
</div>

{{-- ── LAYANAN GRID ────────────────────────────── --}}
<div style="padding: 21px;">
    <div class="divider-label">Layanan Zasha</div>

    <div class="row row-cols-4 g-2">
        @foreach($categories as $kat)
            @php $locked = ($user->is_verif == 0); @endphp
            <div class="col">
                <a href="{{ $locked ? '#' : route('pelanggan.katalog', ['id_kategori' => $kat->id_kategori]) }}"
                   class="menu-btn {{ $locked ? 'locked' : '' }}"
                   {!! $locked ? "onclick='bukaModalLocked(); return false;'" : '' !!}>
                    <div class="menu-icon-wrap">
                        @if($locked)
                            <i class="bi bi-lock-fill" style="color:var(--text-faint);"></i>
                        @else
                            {!! $kat->svg_kategori !!}
                        @endif
                    </div>
                    <span style="color:var(--text-main);font-size:9px;line-height:1.3;">{{ $kat->nama_kategori }}</span>
                </a>
            </div>
        @endforeach
    </div>
</div>

{{-- ── PROMO BANNER ────────────────────────────── --}}
<div style="padding: 0 21px 34px;">
    <div style="background:linear-gradient(135deg,#002d72,#0047b3,#005dd6);
                border-radius:21px; padding:21px; position:relative; overflow:hidden;">
        <div style="position:absolute;top:-21px;right:-21px;width:89px;height:89px;
                    border-radius:50%;background:rgba(240,165,0,.15);"></div>
        <div style="position:absolute;bottom:-13px;right:55px;width:55px;height:55px;
                    border-radius:50%;background:rgba(255,255,255,.07);"></div>
        <div style="display:flex;align-items:center;gap:13px;">
            <div style="width:44px;height:44px;border-radius:13px;background:rgba(240,165,0,.2);
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-rocket-takeoff-fill" style="color:#f0a500;font-size:21px;"></i>
            </div>
            <div>
                <div style="font-size:13px;font-weight:800;color:#fff;">Jastip Jember Siap Antar</div>
                <div style="font-size:10px;color:rgba(255,255,255,.6);margin-top:3px;">
                    Belanja apa saja, diantar ke pintu kamu.
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal locked --}}
<div class="modal fade" id="modalLocked" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:21px;border:none;padding:21px;text-align:center;">
            <div style="width:55px;height:55px;border-radius:13px;background:#fee2e2;
                        display:flex;align-items:center;justify-content:center;margin:0 auto 13px;">
                <i class="bi bi-shield-lock-fill" style="color:#ef4444;font-size:21px;"></i>
            </div>
            <h6 style="font-weight:800;color:var(--text-main);margin-bottom:8px;">Akun Belum Terverifikasi</h6>
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:21px;">
                Lengkapi data profil kamu dulu untuk bisa memesan layanan.
            </p>
            <div class="d-flex gap-2">
                <button class="btn-z-ghost w-100" data-bs-dismiss="modal">Nanti</button>
                <a href="{{ route('pelanggan.profil') }}" class="btn-z-primary w-100 text-center text-decoration-none">Lengkapi</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function bukaModalLocked() {
    new bootstrap.Modal(document.getElementById('modalLocked')).show();
}
</script>
@endpush
@endsection
