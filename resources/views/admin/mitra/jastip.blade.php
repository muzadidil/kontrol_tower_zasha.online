@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="{{ asset('assets/css/admin_jastip.css') }}">
<script src="{{ asset('assets/js/admin_jastip.js') }}" defer></script>

<div id="jastip-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-bicycle text-primary me-2"></i>Monitoring Jastip</h4>
            <small class="text-muted">Pantau aktivitas kurir dan belanjaan pelanggan.</small>
        </div>
        {{-- Tombol kembali (HTMX Dihapus, diganti link biasa ke Dashboard) --}}
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card card-zasha p-3 h-100">
                <small class="text-muted fw-bold d-block mb-1">PROFIT ZASHA</small>
                <h4 class="fw-bold m-0 text-success">Rp {{ number_format($cuan_zasha, 0, ',', '.') }}</h4>
                <div class="mt-2 small text-muted"><i class="bi bi-graph-up text-success"></i> Pendapatan Bersih</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-zasha p-3 h-100">
                <small class="text-muted fw-bold d-block mb-1">ORDER HARI INI</small>
                <h4 class="fw-bold m-0">{{ $order_hari_ini }}</h4>
                <div class="mt-2 small text-muted">Transaksi Masuk</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-zasha p-3 h-100">
                <small class="text-muted fw-bold d-block mb-1">MITRA STANDBY</small>
                <h4 class="fw-bold m-0 text-info">{{ $driver_aktif }}</h4>
                <div class="mt-2 small text-muted">Driver Aktif</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-zasha p-3 h-100 {{ ($total_pending > 0) ? 'card-alert-jastip' : '' }}">
                <small class="text-muted fw-bold d-block mb-1">CARI DRIVER</small>
                <h4 class="fw-bold m-0 text-danger">{{ $total_pending }}</h4>
                <div class="mt-2 small {{ ($total_pending > 0) ? 'text-danger fw-bold' : 'text-muted' }}">
                    {{ ($total_pending > 0) ? 'Perlu Tindakan' : 'Semua Tercover' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card card-zasha overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold m-0 text-dark">Live Transaksi Jastip</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">ID JASTIP</th>
                        <th>PELANGGAN</th>
                        <th>DRIVER / KURIR</th>
                        <th>TOTAL NOTA</th>
                        <th>STATUS</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($all_jastip as $row)
                        @php
                            $status = $row->status_jastip;
                            $badge_class = match($status) {
                                'Selesai' => 'bg-success',
                                'Mencari Driver' => 'bg-danger',
                                'Proses' => 'bg-info',
                                'Dibatalkan' => 'bg-secondary',
                                default => 'bg-warning'
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 fw-bold text-primary">#{{ $row->id_jastip }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_pelanggan }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($row->waktu_order)->format('d M, H:i') }}</div>
                            </td>
                            <td>
                                @if($row->nama_driver)
                                    <span class="text-dark fw-semibold"><i class="bi bi-person-badge me-1"></i> {{ $row->nama_driver }}</span>
                                @else
                                    <span class="text-danger small italic">Belum ada driver</span>
                                @endif
                            </td>
                            <td class="fw-bold">Rp {{ number_format($row->total_pembayaran ?? 0, 0, ',', '.') }}</td>
                            <td><span class="badge badge-pill-zasha {{ $badge_class }}">{{ $status }}</span></td>
                            <td class="text-center">
                                <a href="https://wa.me/{{ $row->no_wa }}" target="_blank" class="btn btn-sm btn-success rounded-circle shadow-sm">
                                    <i class="bi bi-whatsapp"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                                <span class="text-muted">Tidak ada aktivitas jastip yang ditemukan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection