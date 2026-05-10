@extends('layouts.pelanggan')
@section('title', 'Order Tenaga')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Order Tenaga Saya</h4>
    </div>
    @foreach(['success','error','warning','info'] as $t)
        @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif
    @endforeach
    @forelse($orders as $order)
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="fw-bold text-warning">{{ $order->order_code }}</span>
                    <small class="text-muted ms-2">{{ $order->created_at->format('d M Y') }}</small>
                    <div class="small mt-1">{{ $order->durasi }} {{ $order->tipe_durasi }} · {{ ucfirst($order->tipe_waktu) }}</div>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                    <div class="fw-bold small">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="mt-2 d-flex gap-2">
                <a href="{{ route('pelanggan.tenaga.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Detail</a>
                @if($order->status->value === 'menunggu_konfirmasi')
                    <form action="{{ route('pelanggan.tenaga.konfirmasi', $order->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi selesai?')">Konfirmasi</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5 text-muted">Belum ada order tenaga.</div>
    @endforelse
    {{ $orders->links() }}
</div>
@endsection
