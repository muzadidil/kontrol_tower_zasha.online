@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.dashboard') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Jadwal</div>
            <h1 class="m-hero-title">Jam Kerja & Hari Libur</h1>
        </div>
    </div>
    <div class="m-hero-meta" style="margin-top:var(--fib-3);">
        Atur kapan Anda menerima order. Pelanggan akan diberi tahu jam buka Anda.
    </div>
</div>

<div class="m-page">
    @if(session('success'))<div class="m-alert m-alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="m-alert m-alert-error" style="flex-direction:column; align-items:flex-start;">
            @foreach($errors->all() as $err)<div><i class="bi bi-x-circle-fill"></i>{{ $err }}</div>@endforeach
        </div>
    @endif

    {{-- JAM KERJA MINGGUAN --}}
    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">
                <i class="bi bi-calendar-week m-section-title-icon"></i> Jam Kerja Mingguan
            </h2>
            <p style="font-size:var(--t-xxs); color:var(--ink-soft); margin:0 0 var(--fib-3);">Default jam buka per hari</p>

            <form action="{{ route('mitra.jadwal.updateHarian') }}" method="POST">
                @csrf
                @foreach($jadwalHarian as $hari => $j)
                    @php $hariNama = \App\Models\MitraJadwalHarian::HARI[$hari]; @endphp
                    <div id="row-hari-{{ $hari }}"
                         style="border:1px solid var(--line); border-radius:var(--r-sm); padding:var(--fib-2);
                                margin-bottom:var(--fib-2);
                                background:{{ $j->is_libur ? '#fee2e2' : '#f8fbff' }};">
                        <div style="display:flex; align-items:center; gap:var(--fib-2); flex-wrap:wrap;">
                            <div style="min-width:var(--fib-7);">
                                <div style="font-weight:700; color:var(--ink); font-size:var(--t-xs);">{{ $hariNama }}</div>
                            </div>

                            <label style="display:flex; align-items:center; gap:var(--fib-1); font-size:var(--t-xs); cursor:pointer;">
                                <input class="toggle-libur" type="checkbox" name="hari[{{ $hari }}][is_libur]"
                                       data-hari="{{ $hari }}" value="1" id="libur-{{ $hari }}"
                                       {{ $j->is_libur ? 'checked' : '' }}
                                       style="accent-color:var(--mitra-blue);">
                                Libur
                            </label>

                            <div class="jam-input-group" style="margin-left:auto; display:flex; gap:var(--fib-1); align-items:center;
                                                                {{ $j->is_libur ? 'opacity:0.3; pointer-events:none;' : '' }}">
                                <input type="time" name="hari[{{ $hari }}][jam_buka]"
                                       class="m-form-input" style="width:var(--fib-8); padding:var(--fib-1) var(--fib-2);"
                                       value="{{ $j->jam_buka ? substr($j->jam_buka, 0, 5) : '08:00' }}">
                                <span style="color:var(--ink-soft); font-size:var(--t-xs);">—</span>
                                <input type="time" name="hari[{{ $hari }}][jam_tutup]"
                                       class="m-form-input" style="width:var(--fib-8); padding:var(--fib-1) var(--fib-2);"
                                       value="{{ $j->jam_tutup ? substr($j->jam_tutup, 0, 5) : '17:00' }}">
                            </div>
                        </div>
                    </div>
                @endforeach

                <button type="submit" class="m-btn-primary m-btn-primary-block" style="margin-top:var(--fib-3);">
                    <i class="bi bi-check-circle"></i> Simpan Jam Kerja
                </button>
            </form>
        </div>
    </div>

    {{-- TANGGAL LIBUR KHUSUS --}}
    <div class="m-card">
        <div class="m-card-body">
            <h2 class="m-section-title" style="margin-top:0;">
                <i class="bi bi-calendar-x" style="color:#ef4444;"></i> Tanggal Libur Khusus
            </h2>
            <p style="font-size:var(--t-xxs); color:var(--ink-soft); margin:0 0 var(--fib-3);">Cuti / off-day yang tidak mengikuti jadwal mingguan</p>

            <form action="{{ route('mitra.jadwal.storeLibur') }}" method="POST" style="margin-bottom:var(--fib-3);">
                @csrf
                <div style="display:grid; grid-template-columns: 1fr 1.618fr auto; gap:var(--fib-1); align-items:flex-end;">
                    <div>
                        <label class="m-form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="m-form-input"
                               min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label class="m-form-label">Keterangan</label>
                        <input type="text" name="keterangan" class="m-form-input" placeholder="cuti / acara keluarga" maxlength="200">
                    </div>
                    <button type="submit" class="m-btn-primary" style="height:38px;">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
            </form>

            @if($tanggalLibur->isEmpty())
                <div style="text-align:center; padding:var(--fib-3); color:var(--ink-soft); font-size:var(--t-xs);">
                    <i class="bi bi-sun-fill" style="color:#fbbf24;"></i>
                    Tidak ada tanggal libur khusus
                </div>
            @else
                @foreach($tanggalLibur as $l)
                    @php
                        $isPast = $l->tanggal->isPast() && !$l->tanggal->isToday();
                        $isToday = $l->tanggal->isToday();
                    @endphp
                    <div style="display:flex; align-items:center; gap:var(--fib-2); padding:var(--fib-2);
                                border-bottom:1px solid var(--line);
                                {{ $isPast ? 'opacity:0.5;' : '' }}">
                        <div style="text-align:center; min-width:var(--fib-6);">
                            <div style="font-weight:800; color:{{ $isToday ? '#ef4444' : 'var(--ink)' }}; font-size:var(--t-md); line-height:1;">{{ $l->tanggal->format('d') }}</div>
                            <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $l->tanggal->translatedFormat('M Y') }}</div>
                        </div>
                        <div style="flex-grow:1;">
                            <div style="font-weight:700; color:var(--ink); font-size:var(--t-xs);">
                                {{ $l->tanggal->translatedFormat('l') }}
                                @if($isToday)
                                    <span style="background:#ef4444; color:#fff; padding:1px var(--fib-1); border-radius:var(--r-sm); font-size:var(--t-xxs);">Hari ini</span>
                                @elseif($isPast)
                                    <span style="background:var(--line); color:var(--ink-soft); padding:1px var(--fib-1); border-radius:var(--r-sm); font-size:var(--t-xxs);">Lewat</span>
                                @endif
                            </div>
                            @if($l->keterangan)
                                <div style="font-size:var(--t-xxs); color:var(--ink-soft);">{{ $l->keterangan }}</div>
                            @endif
                        </div>
                        <form action="{{ route('mitra.jadwal.destroyLibur', $l->id) }}" method="POST"
                              onsubmit="return confirm('Hapus tanggal libur {{ $l->tanggal->format('d M Y') }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" style="background:none; border:none; color:#ef4444; cursor:pointer; padding:var(--fib-1);">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="m-alert m-alert-info">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Catatan:</strong> Jadwal ini info untuk pelanggan saja.
            Anda tetap perlu set status <strong>"Online"</strong> dari dashboard untuk benar-benar menerima order.
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-libur').forEach(function (cb) {
    cb.addEventListener('change', function () {
        const hari = cb.dataset.hari;
        const row = document.getElementById('row-hari-' + hari);
        const jamGroup = row.querySelector('.jam-input-group');
        if (cb.checked) {
            jamGroup.style.opacity = '0.3';
            jamGroup.style.pointerEvents = 'none';
            row.style.background = '#fee2e2';
        } else {
            jamGroup.style.opacity = '';
            jamGroup.style.pointerEvents = '';
            row.style.background = '#f8fbff';
        }
    });
});
</script>
@endsection
