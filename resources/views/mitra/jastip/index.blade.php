@extends('layouts.mitra')
@section('title', 'Order Jastip')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Order Jastip Masuk</h4>

    @foreach(['success','error','info','warning'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_pickup'=>'info','belanja'=>'info','menuju_pengantaran'=>'primary','diantar'=>'primary','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    @forelse($orders as $order)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="fw-bold text-warning">{{ $order->order_code }}</span>
                    <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} ms-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                    <div class="small text-muted mt-1">{{ $order->total_stops }} stops · {{ $order->total_jarak_km }} km</div>
                    <div class="small text-muted">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold text-success">Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</div>
                    <div class="small text-muted">pendapatan bersih</div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('mitra.jastip.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Lihat Detail</a>
                @if($order->status->value === 'menunggu_mitra')
                    <form action="{{ route('mitra.jastip.terima', $order->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm">Terima</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p class="text-muted">Tidak ada order.</p></div>
    @endforelse

    {{ $orders->links() }}
</div>
@endsection
