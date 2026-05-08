@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/admin_pesanan.css') }}">
<script src="{{ asset('assets/js/admin_pesanan.js') }}" defer></script>

<div id="order-monitoring-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-receipt-cutoff text-primary me-2"></i>Global Monitoring</h4>
            <small class="text-muted">Pantau semua antrian Jasa & Jastip ZASHA.</small>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-white shadow-sm rounded-pill px-3 border bg-white">
            <i class="bi bi-arrow-clockwise"></i>
        </a>
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
                                <span class="text-muted">Tidak ada antrian pesanan saat ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
