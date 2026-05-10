@extends('layouts.admin')
@section('title', 'Monitoring Order WFH')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Monitoring Order WFH</h4>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode order..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach(['pending_pembayaran','menunggu_mitra','dikerjakan','file_terkirim','menunggu_konfirmasi','dispute','selesai','ditolak','refund'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-warning">Filter</button>
                    <a href="{{ route('admin.wfh.index') }}" class="btn btn-outline-secondary ms-1">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Order</th>
                        <th>Pelanggan</th>
                        <th>Mitra</th>
                        <th>Layanan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <span class="fw-bold text-warning">{{ $order->order_code }}</span>
                            @if($order->tipe_order === 'express') <span class="badge bg-warning text-dark ms-1">⚡</span> @endif
                        </td>
                        <td class="small">{{ $order->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td class="small">{{ $order->mitra->nama_asli ?? $order->mitra->nama_panggilan ?? '-' }}</td>
                        <td class="small">{{ $order->mitraLayanan->masterLayanan->nama_layanan ?? '-' }}</td>
                        <td class="small fw-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            @php $bm = ['pending_pembayaran'=>'secondary','menunggu_mitra'=>'warning','dikerjakan'=>'info','file_terkirim'=>'info','menunggu_konfirmasi'=>'primary','dispute'=>'danger','selesai'=>'success','ditolak'=>'danger','refund'=>'secondary']; @endphp
                            <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                        </td>
                        <td class="small text-muted">{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.wfh.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada order.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $orders->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
