@extends('layouts.mitra')
@section('title', 'Order Tenaga')
@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Order Tenaga Masuk</h4>
    @foreach(['success','error','info'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp
    @forelse($orders as $order)
    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <div class="d-flex justify-content-between">
            <div><span class="fw-bold text-warning">{{ $order->order_code }}</span>
                <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} ms-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                <div class="small mt-1">{{ $order->durasi }} {{ $order->tipe_durasi }} · {{ ucfirst($order->tipe_waktu) }}</div>
                <div class="small text-muted">{{ $order->alamat_pelanggan }}</div></div>
            <div class="text-end"><div class="fw-bold text-success">Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</div></div>
        </div>
        <div class="mt-2"><a href="{{ route('mitra.tenaga.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Detail</a></div>
    </div></div>
    @empty <div class="text-center py-5 text-muted">Tidak ada order.</div> @endforelse
    {{ $orders->links() }}
</div>
@endsection
