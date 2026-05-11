@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/admin_pesanan.css') }}">

<div id="jastip-dashboard-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-bag-check-fill text-primary me-2"></i>Dashboard Jastip</h4>
            <small class="text-muted">Ringkasan & antrian order Jastip Hunter.</small>
        </div>
        <a href="{{ route('admin.jastip') }}" class="btn btn-white shadow-sm rounded-pill px-3 border bg-white">
            <i class="bi bi-arrow-clockwise"></i>
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card card-zasha border-0 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <small class="text-muted fw-semibold">Cuan Zasha</small>
                </div>
                <div class="fw-bold text-success" style="font-size:1.1rem;">
                    Rp {{ number_format($cuan_zasha ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-zasha border-0 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <small class="text-muted fw-semibold">Order Hari Ini</small>
                </div>
                <div class="fw-bold text-primary" style="font-size:1.1rem;">{{ $order_hari_ini ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-zasha border-0 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <small class="text-muted fw-semibold">Driver Aktif</small>
                </div>
                <div class="fw-bold text-info" style="font-size:1.1rem;">{{ $driver_aktif ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-zasha border-0 p-3 h-100">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <small class="text-muted fw-semibold">Menunggu Mitra</small>
                </div>
                <div class="fw-bold text-warning" style="font-size:1.1rem;">{{ $total_pending ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card card-zasha p-3 mb-4">
        <form action="{{ route('admin.jastip') }}" method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm rounded-pill px-3">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu_mitra','ditolak','menuju_pickup','belanja','menuju_pengantaran','diantar','menunggu_konfirmasi','selesai','dispute'] as $st)
                        <option value="{{ $st }}" {{ ($filter_status ?? '') === $st ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ', $st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">FILTER</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.jastip') }}" class="btn btn-light btn-sm rounded-pill w-100">RESET</a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Order Info</th>
                        <th>Pelanggan</th>
                        <th>Driver</th>
                        <th>Total Barang</th>
                        <th>Komisi Zasha</th>
                        <th class="text-end pe-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($all_jastip as $row)
                        @php
                            $status_cls = match($row->status_jastip) {
                                'selesai'             => 'bg-success',
                                'menunggu_mitra'      => 'bg-warning',
                                'ditolak'             => 'bg-danger',
                                'dispute'             => 'bg-dark',
                                'menunggu_konfirmasi' => 'bg-info',
                                default               => 'bg-primary',
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">JASTIP</span>
                                    <span class="fw-bold text-dark">#{{ $row->id_jastip }}</span>
                                </div>
                                <div class="text-muted" style="font-size:11px;">
                                    <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($row->waktu_order)->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_pelanggan }}</div>
                                <div class="text-muted small"><i class="bi bi-whatsapp text-success me-1"></i>{{ $row->no_wa }}</div>
                            </td>
                            <td>
                                @if($row->nama_driver)
                                    <div class="fw-semibold text-secondary"><i class="bi bi-person-badge me-1"></i>{{ $row->nama_driver }}</div>
                                @else
                                    <span class="text-muted small fst-italic">Belum ada driver</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">Rp {{ number_format($row->total_harga_barang ?? 0, 0, ',', '.') }}</div>
                                <div class="text-muted" style="font-size:10px;">Ongkir Rp {{ number_format($row->ongkir ?? 0, 0, ',', '.') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-success">Rp {{ number_format($row->total_admin_lokasi ?? 0, 0, ',', '.') }}</div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge {{ $status_cls }}">{{ ucwords(str_replace('_',' ', $row->status_jastip)) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-clipboard-x text-muted fs-1 d-block mb-2"></i>
                                <span class="text-muted">Belum ada order jastip.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($all_jastip->hasPages())
        <div class="mt-3 d-flex justify-content-center">
            {{ $all_jastip->links() }}
        </div>
    @endif
</div>
@endsection
