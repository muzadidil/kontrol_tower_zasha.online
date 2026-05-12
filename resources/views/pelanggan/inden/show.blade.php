@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .header-detail { background: white; position: sticky; top: 0; z-index: 1020; padding: 15px; border-bottom: 1px solid #eee; }
    .card-order { border: none; border-radius: 20px; padding: 20px; background: white; margin: 0 15px 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
    .status-badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; }
    .status-menunggu { background: #fef3c7; color: #b45309; }
    .status-dp { background: #dbeafe; color: #0c4a6e; }
    .status-kerja { background: #d1fae5; color: #065f46; }
    .status-selesai { background: #dcfce7; color: #166534; }
    .section-title { font-size: 12px; font-weight: 800; color: var(--text-faint); text-transform: uppercase; margin: 20px 15px 12px; letter-spacing: 0.5px; }
    .row-info { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .row-info:last-child { border-bottom: none; }
    .row-info-label { color: var(--text-muted); font-size: 13px; }
    .row-info-value { font-weight: 700; color: var(--text-main); font-size: 13px; }
    .price-highlight { color: var(--blue-deep); font-size: 1.2rem; font-weight: 800; }
    .sticky-action { position: fixed; bottom: 70px; left: 0; right: 0; background: white; padding: 12px 20px; border-top: 1px solid #eee; z-index: 999; max-width: 480px; margin: 0 auto; }
    .btn-action { border-radius: 12px; padding: 12px 20px; font-weight: 700; font-size: 13px; border: none; transition: all 0.2s; }
    .btn-primary { background: var(--blue-deep); color: white; }
    .btn-primary:hover { background: #0047b3; }
    .btn-secondary { background: #f3f4f6; color: var(--text-main); }
    .spacer { height: 160px; }
    .timeline { position: relative; padding: 20px 0; }
    .timeline-item { display: flex; gap: 12px; margin-bottom: 20px; }
    .timeline-dot { width: 24px; height: 24px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-size: 11px; color: white; font-weight: 700; flex-shrink: 0; }
    .timeline-dot.done { background: #10b981; }
    .timeline-dot.current { background: var(--blue-deep); }
    .timeline-content { flex: 1; padding-top: 2px; }
    .timeline-title { font-size: 13px; font-weight: 700; color: var(--text-main); }
    .timeline-desc { font-size: 12px; color: var(--text-muted); margin-top: 3px; }
</style>

<div class="header-detail shadow-sm">
    <div class="d-flex align-items-center">
        <a href="javascript:history.back()" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <div>
            <h5 class="fw-bold m-0" style="font-size: 1rem;">Order Inden</h5>
            <small class="text-muted">{{ $order->order_code }}</small>
        </div>
    </div>
</div>

<div class="card-order">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h5 class="fw-bold mb-2">{{ $order->mitra->nama_panggilan }}</h5>
            <span class="status-badge"
                  @if($order->status->value === 'menunggu_mitra') style="background: #fef3c7; color: #b45309;"
                  @elseif(in_array($order->status->value, ['menunggu_dp', 'dp_dibayar'])) style="background: #dbeafe; color: #0c4a6e;"
                  @elseif($order->status->value === 'dikerjakan') style="background: #fce7f3; color: #be123c;"
                  @elseif($order->status->value === 'menunggu_pelunasan') style="background: #fed7aa; color: #92400e;"
                  @elseif($order->status->value === 'selesai') style="background: #dcfce7; color: #166534;"
                  @endif>
                {{ $order->status->label() }}
            </span>
        </div>
        <div class="text-end">
            <small class="d-block text-muted">Jadwal</small>
            <strong class="d-block">{{ $order->tanggal_pelaksanaan->format('d M Y') }}</strong>
        </div>
    </div>
</div>

<div class="section-title">Rincian Pesanan</div>
<div class="card-order">
    <div class="row-info">
        <span class="row-info-label">Durasi Kerja</span>
        <span class="row-info-value">{{ $order->durasi }} {{ ucfirst($order->tipe_durasi) }}</span>
    </div>
    <div class="row-info">
        <span class="row-info-label">Tarif per {{ ucfirst($order->tipe_durasi) }}</span>
        <span class="row-info-value">Rp {{ number_format($order->tarif, 0, ',', '.') }}</span>
    </div>
    <div class="row-info">
        <span class="row-info-label">Total Biaya</span>
        <span class="row-info-value price-highlight">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</span>
    </div>
</div>

<div class="section-title">Pembayaran</div>
<div class="card-order">
    <div class="row-info">
        <span class="row-info-label">DP 50%</span>
        <span class="row-info-value">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</span>
    </div>
    <div class="row-info">
        <span class="row-info-label">Pelunasan 50%</span>
        <span class="row-info-value">Rp {{ number_format($order->pelunasan_amount, 0, ',', '.') }}</span>
    </div>
    <div class="row-info">
        <span class="row-info-label">Komisi Zasha (5%)</span>
        <span class="row-info-value" style="color: #7c3aed;">Rp {{ number_format($order->komisi_zasha, 0, ',', '.') }}</span>
    </div>
    @if($order->dp_paid_at)
    <div class="row-info" style="color: #10b981;">
        <span class="row-info-label">✓ DP Sudah Dibayar</span>
        <span class="row-info-value">{{ $order->dp_paid_at->format('d/m H:i') }}</span>
    </div>
    @endif
    @if($order->pelunasan_paid_at)
    <div class="row-info" style="color: #10b981;">
        <span class="row-info-label">✓ Pelunasan Sudah Dibayar</span>
        <span class="row-info-value">{{ $order->pelunasan_paid_at->format('d/m H:i') }}</span>
    </div>
    @endif
</div>

<div class="section-title">Alamat & Keterangan</div>
<div class="card-order">
    <div class="row-info" style="flex-direction: column; align-items: flex-start;">
        <span class="row-info-label mb-2">Lokasi</span>
        <span class="row-info-value">{{ $order->alamat_pelanggan }}</span>
    </div>
    @if($order->keterangan_kerja)
    <div class="row-info" style="flex-direction: column; align-items: flex-start; border-top: 1px solid #f1f5f9; margin-top: 12px; padding-top: 12px;">
        <span class="row-info-label mb-2">Keterangan</span>
        <span class="row-info-value">{{ $order->keterangan_kerja }}</span>
    </div>
    @endif
</div>

<div class="spacer"></div>

<div class="sticky-action shadow">
    @if($order->status->value === 'menunggu_mitra')
        <p class="small text-muted text-center mb-3">Menunggu approval dari mitra...</p>
        <button class="btn btn-secondary w-100 btn-action" disabled>Sedang Diproses</button>

    @elseif($order->status->value === 'menunggu_dp')
        <p class="small text-muted text-center mb-3">Siap membayar DP Rp {{ number_format($order->dp_amount, 0, ',', '.') }}?</p>
        <form action="{{ route('pelanggan.inden.bayar-dp', $order->id) }}" method="POST" class="d-inline w-100">
            @csrf
            <button type="submit" class="btn btn-primary w-100 btn-action">BAYAR DP SEKARANG</button>
        </form>

    @elseif($order->status->value === 'dp_dibayar')
        <p class="small text-muted text-center mb-3">✓ DP sudah dibayar. Menunggu hari H...</p>
        <button class="btn btn-secondary w-100 btn-action" disabled>Menunggu Mitra Tiba</button>

    @elseif($order->status->value === 'dikerjakan')
        <p class="small text-muted text-center mb-3">Siap membayar pelunasan Rp {{ number_format($order->pelunasan_amount, 0, ',', '.') }}?</p>
        <form action="{{ route('pelanggan.inden.bayar-pelunasan', $order->id) }}" method="POST" class="d-inline w-100">
            @csrf
            <button type="submit" class="btn btn-primary w-100 btn-action">BAYAR PELUNASAN</button>
        </form>

    @elseif($order->status->value === 'menunggu_pelunasan')
        <p class="small text-muted text-center mb-3">✓ Pelunasan sudah dibayar. Konfirmasi pekerjaan selesai?</p>
        <form action="{{ route('pelanggan.inden.konfirmasi', $order->id) }}" method="POST" class="d-inline w-100">
            @csrf
            <button type="submit" class="btn btn-primary w-100 btn-action">KONFIRMASI SELESAI</button>
        </form>
        <button type="button" class="btn btn-secondary w-100 btn-action mt-2" data-bs-toggle="modal" data-bs-target="#modalDispute">
            BUKA DISPUTE
        </button>

    @elseif($order->status->value === 'selesai')
        <p class="small text-success text-center mb-3">✓ Order selesai. Terima kasih!</p>
        <button class="btn btn-secondary w-100 btn-action" disabled>Pesanan Selesai</button>

    @elseif($order->status->value === 'ditolak')
        <p class="small text-danger text-center mb-3">✗ Order ditolak mitra</p>
        <button class="btn btn-secondary w-100 btn-action" disabled>Ditolak</button>

    @elseif($order->status->value === 'dispute')
        <p class="small text-warning text-center mb-3">⚠ Dispute sedang dalam proses</p>
        <button class="btn btn-secondary w-100 btn-action" disabled>Dalam Dispute</button>
    @endif
</div>

@if($order->status->value === 'menunggu_dp' || $order->status->value === 'dikerjakan' || $order->status->value === 'menunggu_pelunasan')
<div style="padding: 20px 15px; color: #ef4444; font-size: 12px; text-align: center;">
    <i class="bi bi-exclamation-circle me-1"></i>Pembayaran dengan metode {{ ucfirst($order->metode_pembayaran) }}
</div>
@endif

{{-- Modal Dispute --}}
<div class="modal fade" id="modalDispute" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered px-3">
        <div class="modal-content" style="border-radius: 25px; border: none;">
            <div class="modal-body p-4">
                <h5 class="fw-bold mb-3 text-center">Buka Dispute</h5>
                <form action="{{ route('pelanggan.inden.dispute', $order->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Jelaskan Masalahnya</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="4"
                                  placeholder="Jelaskan masalah yang terjadi..." required minlength="20"></textarea>
                        @error('deskripsi')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold py-2"
                                data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2">KIRIM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
