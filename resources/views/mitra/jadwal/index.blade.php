@extends('layouts.mitra')

@section('content')
{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">JADWAL</div>
            <h5 class="fw-bold m-0">Jam Kerja & Hari Libur</h5>
        </div>
    </div>
    <div class="mt-3 text-center" style="font-size:.75rem;opacity:.85;">
        Atur kapan Anda menerima order. Pelanggan akan diberi tahu jam buka Anda.
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-3 small">
            @foreach($errors->all() as $err)
                <div><i class="bi bi-x-circle-fill me-1"></i>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- ── JAM KERJA MINGGUAN ── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold m-0" style="color:#1e293b;">
                        <i class="bi bi-calendar-week me-1" style="color:#005aa9;"></i>Jam Kerja Mingguan
                    </h6>
                    <small class="text-muted">Default jam buka per hari</small>
                </div>
            </div>

            <form action="{{ route('mitra.jadwal.updateHarian') }}" method="POST">
                @csrf
                @foreach($jadwalHarian as $hari => $j)
                    @php $hariNama = \App\Models\MitraJadwalHarian::HARI[$hari]; @endphp
                    <div class="border rounded-3 p-2 mb-2"
                         id="row-hari-{{ $hari }}"
                         style="background:{{ $j->is_libur ? '#fef2f2' : '#f8fafc' }};">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div style="min-width:80px;">
                                <div class="fw-bold small" style="color:#1e293b;">{{ $hariNama }}</div>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-libur"
                                       type="checkbox" role="switch"
                                       name="hari[{{ $hari }}][is_libur]"
                                       data-hari="{{ $hari }}"
                                       value="1"
                                       id="libur-{{ $hari }}"
                                       {{ $j->is_libur ? 'checked' : '' }}>
                                <label class="form-check-label small" for="libur-{{ $hari }}">
                                    Libur
                                </label>
                            </div>

                            <div class="ms-auto d-flex gap-1 align-items-center jam-input-group"
                                 style="{{ $j->is_libur ? 'opacity:.3;pointer-events:none;' : '' }}">
                                <input type="time" name="hari[{{ $hari }}][jam_buka]"
                                       class="form-control form-control-sm rounded-2"
                                       value="{{ $j->jam_buka ? substr($j->jam_buka, 0, 5) : '08:00' }}"
                                       style="width:90px;">
                                <span class="text-muted small">—</span>
                                <input type="time" name="hari[{{ $hari }}][jam_tutup]"
                                       class="form-control form-control-sm rounded-2"
                                       value="{{ $j->jam_tutup ? substr($j->jam_tutup, 0, 5) : '17:00' }}"
                                       style="width:90px;">
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                        <i class="bi bi-check-circle me-1"></i>Simpan Jam Kerja
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── TANGGAL LIBUR KHUSUS ── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <div class="mb-3">
                <h6 class="fw-bold m-0" style="color:#1e293b;">
                    <i class="bi bi-calendar-x me-1" style="color:#ef4444;"></i>Tanggal Libur Khusus
                </h6>
                <small class="text-muted">Cuti / off-day yang tidak mengikuti jadwal mingguan</small>
            </div>

            <form action="{{ route('mitra.jadwal.storeLibur') }}" method="POST" class="mb-3">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-5">
                        <label class="form-label small mb-1">Tanggal</label>
                        <input type="date" name="tanggal"
                               class="form-control form-control-sm rounded-2"
                               min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                               required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small mb-1">Keterangan</label>
                        <input type="text" name="keterangan"
                               class="form-control form-control-sm rounded-2"
                               placeholder="cuti / acara keluarga"
                               maxlength="200">
                    </div>
                    <div class="col-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill w-100">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                </div>
            </form>

            @if($tanggalLibur->isEmpty())
                <div class="text-center py-3 text-muted small">
                    <i class="bi bi-sun-fill me-1" style="color:#fbbf24;"></i>
                    Tidak ada tanggal libur khusus
                </div>
            @else
                @foreach($tanggalLibur as $l)
                    @php
                        $isPast = $l->tanggal->isPast() && !$l->tanggal->isToday();
                        $isToday = $l->tanggal->isToday();
                    @endphp
                    <div class="d-flex align-items-center gap-2 p-2 border-bottom {{ $isPast ? 'opacity-50' : '' }}">
                        <div class="text-center" style="min-width:50px;">
                            <div class="fw-bold" style="color:{{ $isToday ? '#ef4444' : '#1e293b' }};font-size:1.1rem;">
                                {{ $l->tanggal->format('d') }}
                            </div>
                            <small class="text-muted" style="font-size:.65rem;">
                                {{ $l->tanggal->translatedFormat('M Y') }}
                            </small>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small fw-semibold" style="color:#1e293b;">
                                {{ $l->tanggal->translatedFormat('l') }}
                                @if($isToday)
                                    <span class="badge bg-danger ms-1" style="font-size:.6rem;">Hari ini</span>
                                @elseif($isPast)
                                    <span class="badge bg-secondary ms-1" style="font-size:.6rem;">Lewat</span>
                                @endif
                            </div>
                            @if($l->keterangan)
                                <small class="text-muted">{{ $l->keterangan }}</small>
                            @endif
                        </div>
                        <form action="{{ route('mitra.jadwal.destroyLibur', $l->id) }}" method="POST"
                              onsubmit="return confirm('Hapus tanggal libur {{ $l->tanggal->format('d M Y') }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-link text-danger p-1">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Info status online --}}
    <div class="alert alert-info rounded-3 small mb-0"
         style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>Catatan:</strong> Jadwal ini info untuk pelanggan saja.
        Anda tetap perlu set status <strong>"Online"</strong> dari dashboard untuk benar-benar menerima order.
    </div>
</div>

<script>
// Toggle libur: disable/enable input jam pada baris yang sama
document.querySelectorAll('.toggle-libur').forEach(function (cb) {
    cb.addEventListener('change', function () {
        const hari = cb.dataset.hari;
        const row = document.getElementById('row-hari-' + hari);
        const jamGroup = row.querySelector('.jam-input-group');
        if (cb.checked) {
            jamGroup.style.opacity = '.3';
            jamGroup.style.pointerEvents = 'none';
            row.style.background = '#fef2f2';
        } else {
            jamGroup.style.opacity = '';
            jamGroup.style.pointerEvents = '';
            row.style.background = '#f8fafc';
        }
    });
});
</script>
@endsection
