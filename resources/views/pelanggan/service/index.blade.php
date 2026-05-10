@extends('layouts.pelanggan')
@section('title', 'Order Service')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4">Order Service Saya</h4>
    @foreach(['success','error','warning'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','diagnosa'=>'info','menunggu_konfirmasi_harga'=>'warning','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp
    @forelse($orders as $order)
    <div class="card shadow-sm mb-3 border-0"><div class="card-body">
        <div class="d-flex justify-content-between">
            <div><span class="fw-bold text-warning">{{ $order->order_code }}</span>
                <small class="text-muted ms-2">{{ $order->created_at->format('d M Y') }}</small></div>
            <div class="text-end"><span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span></div>
        </div>
        <div class="mt-2"><a href="{{ route('pelanggan.service.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Detail</a></div>
    </div></div>
    @empty <div class="text-center py-5 text-muted">Belum ada order service.</div> @endforelse
    {{ $orders->links() }}
</div>
@endsection
