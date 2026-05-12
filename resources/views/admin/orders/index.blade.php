@extends('layouts.admin')

@section('content')
<style>
    .badge-pill-zasha {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        color: white;
    }
    .badge-pill-order {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .text-purple { color: #9333ea !important; }
    .bg-purple { background-color: #9333ea !important; }
    .card-zasha { border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background: white; }
    .table-zasha th { background: #f9fafb; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 700; padding: 12px 8px; }
    .table-zasha td { padding: 14px 8px; font-size: 13px; vertical-align: middle; }
    .select-quick-action { max-width: 110px; font-size: 11px; }
</style>

<div id="order-monitoring-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Pesanan Aktif</h4>
            <small class="text-muted">Hanya tampilkan pesanan yang sedang berjalan. Untuk pesanan Selesai/Batal, lihat <a href="{{ route('admin.orders.arsip') }}">Arsip Pesanan</a>.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders.arsip') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-archive me-1"></i> Arsip
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-white shadow-sm rounded-pill px-3 border bg-white">
                <i class="bi bi-arrow-clockwise"></i>
            </a>
        </div>
    </div>

    <div class="card card-zasha p-3 mb-4">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 rounded-end-pill" placeholder="Cari ID, Pelanggan, atau Mitra..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm rounded-pill px-3">
                    <option value="">Semua Status</option>
                    @foreach(['Pending', 'Proses', 'Lunas', 'Menunggu Konfirmasi'] as $st)
                        <option value="{{ $st }}" {{ $filter_status == $st ? 'selected' : '' }}>{{ $st == 'Menunggu Konfirmasi' ? 'Konfirmasi' : $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">FILTER</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light btn-sm rounded-pill w-100">RESET</a>
            </div>
        </form>
    </div>

    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order Info</th>
                        <th>Pelanggan</th>
                        <th>Mitra/Pekerja</th>
                        <th>Tagihan</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $row)
                        @php
                            $st = $row->status;
                            $tipe = $row->tipe_order;
                            $tipe_cls = ($tipe == 'JASA') ? 'text-primary bg-primary' : 'text-purple bg-purple';
                            $status_cls = match($st) {
                                'Proses' => 'bg-info',
                                'Lunas' => 'bg-success',
                                'Menunggu Konfirmasi' => 'bg-dark',
                                'Pending' => 'bg-warning',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge badge-pill-order bg-opacity-10 {{ $tipe_cls }}">{{ $tipe }}</span>
                                    <span class="fw-bold text-dark">#{{ $row->id }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 11px;"><i class="bi bi-clock me-1"></i>{{ date('d M, H:i', strtotime($row->tgl)) }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_pelanggan }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 150px; font-size: 11px;">{{ $row->detail }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary"><i class="bi bi-person-badge me-1"></i>{{ $row->nama_pekerja }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">Rp {{ number_format($row->total_biaya, 0, ',', '.') }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $row->metode }}</div>
                            </td>
                            <td>
                                <span class="badge badge-pill-zasha {{ $status_cls }}">{{ $st }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.orders.updateStatus') }}" method="POST" class="d-flex gap-1 justify-content-end align-items-center">
                                    @csrf
                                    <input type="hidden" name="id_pesanan" value="{{ $row->id }}">
                                    <input type="hidden" name="tipe_order" value="{{ $tipe }}">
                                    <select name="status_baru" class="form-select form-select-sm py-1 px-2 select-quick-action">
                                        <option value="Proses" {{ $st == 'Proses' ? 'disabled' : '' }}>Proses</option>
                                        <option value="Lunas" {{ $st == 'Lunas' ? 'disabled' : '' }}>Lunas</option>
                                        <option value="Selesai">Selesai</option>
                                        <option value="Batal">Batal</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-2 shadow-sm"><i class="bi bi-check-lg"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-clipboard-x text-muted fs-1 d-block mb-2"></i>
                                <div class="text-muted mb-1"><strong>Tidak ada pesanan aktif.</strong></div>
                                <div class="text-muted small">
                                    Pesanan yang sudah Selesai atau Dibatalkan ada di
                                    <a href="{{ route('admin.orders.arsip') }}">Arsip Pesanan</a>.
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
