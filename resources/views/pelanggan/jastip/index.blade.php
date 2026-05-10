@extends('layouts.pelanggan')
@section('title', 'Order Jastip Saya')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Order Jastip Saya</h4>
        <a href="{{ route('pelanggan.jastip.create') }}" class="btn btn-warning btn-sm fw-semibold">
            <i class="fas fa-plus me-1"></i> Order Baru
        </a>
    </div>

    @foreach(['success','error','warning','info'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    @forelse($orders as $order)
    @php
        $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_pickup'=>'info','belanja'=>'info','menuju_pengantaran'=>'primary','diantar'=>'primary','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger'];
    @endphp
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div>
                    <span class="fw-bold text-warning">{{ $order->order_code }}</span>
                    <small class="text-muted ms-2">{{ $order->created_at->format('d M Y, H:i') }}</small>
                    <div class="small text-muted mt-1">{{ $order->total_stops }} stops · {{ $order->total_jarak_km }} km</div>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} mb-1">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                    <div class="fw-bold small">Ongkos: Rp {{ number_format($order->ongkos_jasa, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="mt-2 d-flex gap-2">
                <a href="{{ route('pelanggan.jastip.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Detail</a>
                @if($order->status->value === 'menunggu_konfirmasi')
                    <form action="{{ route('pelanggan.jastip.konfirmasi', $order->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi barang sudah diterima?')">
                            <i class="fas fa-check"></i> Konfirmasi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
        <p class="text-muted">Belum ada order Jastip. <a href="{{ route('pelanggan.jastip.create') }}">Order sekarang</a></p>
    </div>
    @endforelse

    {{ $orders->links() }}
</div>
@endsection
