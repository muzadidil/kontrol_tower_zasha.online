@extends('layouts.admin')

@section('content')
<style>
    .badge-arsip {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-selesai { background: #d1fae5; color: #065f46; }
    .status-batal   { background: #fee2e2; color: #991b1b; }
    .row-arsip:hover { background: #f9fafb; }
    .label-arsip { font-size: 11px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block; }
    .small-text { font-size: 11px; }
    .mitra-tag { font-size: 11px; color: #6b7280; margin-top: 2px; }
    .detail-jasa { font-size: 12px; }
    .metode-text { font-size: 11px; }
    .select-koreksi { max-width: 140px; font-size: 11px; }
    .card-zasha { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background: white; }
    .table-zasha th { background: #f9fafb; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 700; padding: 12px 8px; }
    .table-zasha td { padding: 14px 8px; font-size: 13px; vertical-align: middle; }
</style>

<div id="riwayat-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-archive-fill text-secondary me-2"></i>Arsip Pesanan</h4>
            <small class="text-muted">Data transaksi yang telah selesai atau dibatalkan.</small>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>PESANAN AKTIF
        </a>
    </div>

    <div class="card card-zasha p-4 mb-4">
        <form action="{{ route('admin.orders.arsip') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="label-arsip">Pencarian Cepat</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 rounded-end-3" placeholder="ID, Nama, Mitra..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <label class="label-arsip">Status</label>
                <select name="status" class="form-select form-select-sm rounded-3">
                    <option value="">Semua Arsip</option>
                    <option value="Selesai" {{ $filter_status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="Batal" {{ $filter_status == 'Batal' ? 'selected' : '' }}>Batal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="label-arsip">Nama Pelanggan</label>
                <input type="text" name="nama" class="form-control form-control-sm rounded-3" value="{{ $filter_nama }}" placeholder="Filter nama...">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold w-100">APPLY FILTER</button>
                <a href="{{ route('admin.orders.arsip') }}" class="btn btn-light btn-sm rounded-pill px-3 border">RESET</a>
            </div>
        </form>
    </div>

    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID / Tanggal</th>
                        <th>Pihak Terlibat</th>
                        <th>Detail Layanan</th>
                        <th>Total Biaya</th>
                        <th>Status Akhir</th>
                        <th class="text-end pe-4">Aksi Koreksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $row)
                        @php
                            $display_harga = $row->biaya_jasa > 0 ? $row->biaya_jasa : ($row->total_pesanan + $row->ongkir + $row->kode_unik);
                            $is_batal = ($row->status_pesanan == 'Batal');
                        @endphp
                        <tr class="row-arsip">
                            <td class="ps-4">
                                <div class="fw-bold text-dark">#{{ $row->id_pesanan }}</div>
                                <div class="text-muted small-text">{{ date('d M Y, H:i', strtotime($row->tanggal_pesanan)) }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_pelanggan }}</div>
                                <div class="mitra-tag">
                                    <i class="bi bi-person-badge me-1"></i> Mitra: {{ $row->nama_mitra ?: 'N/A' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-uppercase detail-jasa">{{ $row->kategori_jasa }}</div>
                                <div class="text-muted metode-text">{{ $row->metode_pembayaran }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp {{ number_format($display_harga, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <span class="badge-arsip {{ $is_batal ? 'status-batal' : 'status-selesai' }}">
                                    {{ $row->status_pesanan }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <form action="{{ route('admin.orders.arsip.update') }}" method="POST" class="d-flex justify-content-end gap-1">
                                    @csrf
                                    <input type="hidden" name="id_pesanan" value="{{ $row->id_pesanan }}">
                                    <select name="status_baru" class="form-select form-select-sm select-koreksi">
                                        <option value="Selesai" {{ !$is_batal ? 'selected' : '' }}>Tetap Selesai</option>
                                        <option value="Batal" {{ $is_batal ? 'selected' : '' }}>Tetap Batal</option>
                                        <option value="Lunas">Buka Lagi (Lunas)</option>
                                        <option value="Pending">Buka Lagi (Pending)</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-secondary rounded-3 px-2 shadow-sm"><i class="bi bi-arrow-counterclockwise"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="opacity-25">
                                    <i class="bi bi-clipboard-x display-1"></i>
                                    <h6 class="fw-bold mt-3">Tidak Ada Data Arsip.</h6>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
