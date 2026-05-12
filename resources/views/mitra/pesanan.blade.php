@extends('layouts.mitra')

@section('content')
<div class="page-pad stack-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center" style="padding-top: var(--fib-2);">
        <div>
            <div class="label-up">Order Saya</div>
            <h5 class="fw-bold mb-0 t-lg" style="margin-top: var(--fib-1);">Daftar Pesanan</h5>
        </div>
        <div style="width: var(--fib-5); height: var(--fib-5); background: var(--mitra-blue-soft); border-radius: var(--r-md); display: inline-flex; align-items: center; justify-content: center;">
            <span class="fw-bold" style="color: var(--mitra-blue); font-size: var(--t-sm);">{{ $counters->total ?? 0 }}</span>
        </div>
    </div>

    {{-- Filter Tabs (5 tabs dengan counter badge) --}}
    <div class="d-flex" style="gap: var(--fib-2); overflow-x: auto; padding-bottom: var(--fib-2);">
        @php
            $tabs = [
                'semua'      => ['Semua', 'bi-collection', $counters->total ?? 0],
                'aktif'      => ['Aktif', 'bi-clock', $counters->aktif ?? 0],
                'perbaikan'  => ['Perbaikan', 'bi-tools', $counters->perbaikan ?? 0],
                'selesai'    => ['Selesai', 'bi-check-circle', $counters->selesai ?? 0],
                'dibatalkan' => ['Batal', 'bi-x-circle', $counters->dibatalkan ?? 0],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            @php
                $isActive = $filter === $key;
                $hasCount = ($tab[2] ?? 0) > 0;
            @endphp
            <a href="{{ route('mitra.pesanan', ['filter' => $key]) }}" class="btn"
                style="background: {{ $isActive ? 'var(--mitra-blue)' : 'var(--surface)' }};
                       color: {{ $isActive ? '#fff' : 'var(--ink-soft)' }};
                       border: 1px solid {{ $isActive ? 'var(--mitra-blue)' : 'var(--line)' }};
                       border-radius: var(--r-pill);
                       font-size: var(--t-xs); font-weight: 700;
                       padding: var(--fib-2) var(--fib-3); white-space: nowrap;
                       text-decoration: none; display: inline-flex; align-items: center; gap: var(--fib-1);">
                <i class="bi {{ $tab[1] }}"></i>
                <span>{{ $tab[0] }}</span>
                @if($hasCount)
                    <span style="background: {{ $isActive ? 'rgba(255,255,255,0.25)' : 'var(--mitra-blue-soft)' }};
                                 color: {{ $isActive ? '#fff' : 'var(--mitra-blue)' }};
                                 border-radius: 999px; padding: 1px 6px; font-size: 10px; font-weight: 800;">
                        {{ $tab[2] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- List --}}
    @if($pesanan->isEmpty())
        <div class="card-custom text-center" style="padding: var(--fib-6) var(--fib-4);">
            <div style="width: var(--fib-7); height: var(--fib-7); background: var(--bg-app); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto var(--fib-3);">
                <i class="bi bi-clipboard-x" style="font-size: var(--fib-5); color: var(--ink-soft);"></i>
            </div>
            <h6 class="fw-bold t-sm mb-1">Belum Ada Pesanan</h6>
            <div class="t-xs" style="color: var(--ink-soft);">
                @if($filter === 'semua')
                    Pesanan baru akan muncul di sini.
                @else
                    Tidak ada pesanan dengan kategori "{{ ucfirst($filter) }}".
                @endif
            </div>
        </div>
    @else
        <div class="stack-3">
            @foreach($pesanan as $p)
                @php
                    // Mapping status -> label, color, action
                    $statusMeta = match($p->status) {
                        'pending'        => ['Menunggu Respons', '#fef9c3', '#854d0e', 'bi-clock-history'],
                        'accepted'       => ['Diterima', '#dbeafe', '#1e40af', 'bi-check-circle'],
                        'menuju_lokasi'  => ['Menuju Lokasi', '#dbeafe', '#1e40af', 'bi-geo-alt'],
                        'di_lokasi'      => ['Di Lokasi', '#dbeafe', '#1e40af', 'bi-pin-map'],
                        'dikerjakan'     => ['Dikerjakan', '#dbeafe', '#1e40af', 'bi-hammer'],
                        'selesai_mitra'  => ['Menunggu Konfirmasi Pelanggan', '#fef3c7', '#92400e', 'bi-hourglass-split'],
                        'belum_selesai'  => ['Perlu Diperbaiki', '#fed7aa', '#9a3412', 'bi-tools'],
                        'selesai'        => ['Selesai', '#d1fae5', '#065f46', 'bi-patch-check-fill'],
                        'ditolak_mitra'  => ['Ditolak', '#fee2e2', '#991b1b', 'bi-x-circle'],
                        'dibatalkan'     => ['Dibatalkan', '#e5e7eb', '#374151', 'bi-x-circle'],
                        default          => [ucfirst($p->status), '#e5e7eb', '#374151', 'bi-circle'],
                    };
                    $namaPel = $p->pelanggan_nama ?? $p->pelanggan_nama_full ?? 'Pelanggan';
                @endphp

                <div class="card-custom" style="padding: var(--fib-3);">
                    {{-- Header row: ID + status badge --}}
                    <div class="d-flex justify-content-between align-items-center" style="margin-bottom: var(--fib-2);">
                        <span class="fw-bold allow-select t-sm" style="font-family: 'SF Mono', monospace;">
                            #{{ $p->tracking_id }} · {{ ucfirst($p->order_type) }}
                        </span>
                        <span style="background: {{ $statusMeta[1] }}; color: {{ $statusMeta[2] }};
                                     font-size: var(--t-xxs); padding: var(--fib-1) var(--fib-3);
                                     border-radius: var(--r-pill); font-weight: 800; letter-spacing: 0.05em;">
                            <i class="bi {{ $statusMeta[3] }} me-1"></i>{{ $statusMeta[0] }}
                        </span>
                    </div>

                    {{-- Pelanggan + tanggal --}}
                    <div style="margin-bottom: var(--fib-2);">
                        <div class="d-flex align-items-center" style="gap: var(--fib-2); margin-bottom: var(--fib-1);">
                            <i class="bi bi-person-circle" style="color: var(--mitra-blue); font-size: var(--t-md);"></i>
                            <div style="font-weight: 700; color: var(--ink); font-size: var(--t-sm);">{{ $namaPel }}</div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: var(--fib-1); color: var(--ink-soft); font-size: var(--t-xxs); margin-left: 22px;">
                            <i class="bi bi-calendar3"></i>
                            <span>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y · H:i') }}</span>
                        </div>
                    </div>

                    {{-- Banner khusus untuk belum_selesai --}}
                    @if($p->status === 'belum_selesai')
                        <div style="background: #fff7ed; border: 1.5px solid #fb923c; border-radius: var(--r-md);
                                    padding: var(--fib-2); margin-bottom: var(--fib-2); font-size: var(--t-xxs);
                                    color: #9a3412; line-height: 1.5;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Pelanggan minta perbaikan.</strong> Selesaikan perbaikan lalu klik tombol di bawah.
                        </div>
                    @endif

                    {{-- Footer: pendapatan + actions --}}
                    <div class="d-flex justify-content-between align-items-center"
                         style="padding-top: var(--fib-2); border-top: 1px solid var(--line); gap: var(--fib-2);">
                        <div>
                            <div class="t-xxs label-up">Pendapatan</div>
                            <div class="fw-bold t-sm" style="color: var(--mitra-blue);">
                                Rp {{ number_format($p->harga_modal ?? 0, 0, ',', '.') }}
                            </div>
                        </div>

                        {{-- Action button sesuai status --}}
                        <div style="display: flex; gap: var(--fib-1); flex-wrap: wrap; justify-content: flex-end;">
                            @if($p->status === 'belum_selesai')
                                <button type="button"
                                        onclick="markPerbaikanSelesai({{ $p->tracking_id }}, this)"
                                        style="background: #10b981; color: white; border: none; border-radius: var(--r-pill);
                                               padding: var(--fib-2) var(--fib-3); font-size: var(--t-xxs); font-weight: 800;
                                               cursor: pointer; display: inline-flex; align-items: center; gap: var(--fib-1);">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Sudah Diperbaiki
                                </button>
                            @elseif($p->status === 'selesai_mitra')
                                <span style="font-size: var(--t-xxs); color: #92400e; font-weight: 700;
                                             padding: var(--fib-2); display: inline-flex; align-items: center; gap: var(--fib-1);">
                                    <i class="bi bi-hourglass-split"></i> Menunggu pelanggan
                                </span>
                            @elseif(in_array($p->status, ['accepted','menuju_lokasi','di_lokasi','dikerjakan']))
                                <a href="{{ route('mitra.dashboard') }}" class="btn-mitra-primary"
                                   style="padding: var(--fib-2) var(--fib-3); font-size: var(--t-xxs);
                                          text-decoration: none; display: inline-flex; align-items: center; gap: var(--fib-1);">
                                    <i class="bi bi-arrow-up-right-circle"></i> Update Progress
                                </a>
                            @endif

                            @if(!empty($p->pelanggan_wa))
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->pelanggan_wa) }}"
                                   target="_blank"
                                   style="background: #25d366; color: white; border-radius: var(--r-pill);
                                          padding: var(--fib-2) var(--fib-3); font-size: var(--t-xxs); font-weight: 800;
                                          text-decoration: none; display: inline-flex; align-items: center; gap: var(--fib-1);">
                                    <i class="bi bi-whatsapp"></i> WA
                                </a>
                            @endif
                        </div>
                    </div>

                    @if(!empty($p->pesan_tolak))
                        <div style="margin-top: var(--fib-2); padding: var(--fib-2); background: #fef2f2;
                                    border-radius: var(--r-md); font-size: var(--t-xxs); color: #991b1b;">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Alasan ditolak:</strong> {{ $p->pesan_tolak }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>

@push('scripts')
<script>
async function markPerbaikanSelesai(trackingId, btn) {
    if (!confirm('Yakin perbaikan sudah selesai? Pelanggan akan diminta konfirmasi ulang.')) return;

    btn.disabled = true;
    const oriHtml = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Mengirim...';

    try {
        const resp = await fetch('{{ route("mitra.order.progress") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tracking_id: trackingId, status: 'selesai_mitra' })
        });
        const data = await resp.json();

        if (data.success) {
            // Reload halaman untuk refresh tampilan & counter
            location.reload();
        } else {
            alert(data.message || 'Gagal mengirim status. Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = oriHtml;
        }
    } catch (e) {
        console.error('markPerbaikanSelesai failed:', e);
        alert('Gagal mengirim status. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = oriHtml;
    }
}
</script>
@endpush

@endsection
