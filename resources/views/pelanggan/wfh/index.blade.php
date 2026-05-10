@extends('layouts.pelanggan')
@section('title', 'Order WFH Saya')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Order WFH Saya</h4>
        <a href="{{ route('pelanggan.katalog', 'WFH') }}" class="btn btn-warning btn-sm fw-semibold">
            <i class="fas fa-plus me-1"></i> Order Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @forelse($orders as $order)
    <div class="card shadow-sm mb-3 border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="fw-bold text-warning">{{ $order->order_code }}</span>
                    <small class="text-muted ms-2">{{ $order->created_at->format('d M Y, H:i') }}</small>
                    <div class="mt-1 text-muted small">{{ Str::limit($order->brief_description, 80) }}</div>
                </div>
                <div class="text-end">
                    @php
                        $badgeMap = [
                            'pending_pembayaran'  => 'secondary',
                            'menunggu_mitra'      => 'warning',
                            'ditolak'             => 'danger',
                            'dikerjakan'          => 'info',
                            'file_terkirim'       => 'info',
                            'menunggu_konfirmasi' => 'primary',
                            'dispute'             => 'danger',
                            'selesai'             => 'success',
                            'refund'              => 'secondary',
                        ];
                        $badge = $badgeMap[$order->status->value] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $badge }} mb-1">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                    <div class="fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="mt-2 d-flex gap-2">
                <a href="{{ route('pelanggan.wfh.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Lihat Detail</a>
                @if($order->status->value === 'menunggu_konfirmasi')
                    <form action="{{ route('pelanggan.wfh.konfirmasi', $order->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi pekerjaan selesai?')">
                            <i class="fas fa-check me-1"></i> Konfirmasi Selesai
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="fas fa-laptop-code fa-3x text-muted mb-3"></i>
        <p class="text-muted">Belum ada order WFH. <a href="{{ route('pelanggan.katalog', 'WFH') }}">Pesan sekarang</a></p>
    </div>
    @endforelse

    {{ $orders->links() }}
</div>
@endsection
