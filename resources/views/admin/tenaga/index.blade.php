@extends('layouts.admin')
@section('title', 'Monitoring Tenaga')
@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Monitoring Order Tenaga</h4>
    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari order..." value="{{ request('search') }}"></div>
            <div class="col-md-3"><select name="status" class="form-select"><option value="">Semua</option>@foreach(['menunggu_mitra','menuju_lokasi','dikerjakan','menunggu_konfirmasi','selesai','ditolak','dispute'] as $s)<option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>@endforeach</select></div>
            <div class="col-auto"><button class="btn btn-warning">Filter</button></div>
        </form>
    </div></div>
    <div class="card border-0 shadow-sm"><div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Kode</th><th>Pelanggan</th><th>Mitra</th><th>Durasi</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($orders as $order)
                @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp
                <tr>
                    <td class="fw-bold text-warning">{{ $order->order_code }}</td>
                    <td class="small">{{ $order->pelanggan->nama_pelanggan ?? '-' }}</td>
                    <td class="small">{{ $order->mitra->nama_asli ?? '-' }}</td>
                    <td class="small">{{ $order->durasi }} {{ $order->tipe_durasi }}</td>
                    <td class="small">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</td>
                    <td><span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span></td>
                    <td><a href="{{ route('admin.tenaga.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Detail</a></td>
                </tr>
                @empty <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada order.</td></tr> @endforelse
            </tbody>
        </table>
    </div><div class="card-footer bg-white">{{ $orders->withQueryString()->links() }}</div></div>
</div>
@endsection
