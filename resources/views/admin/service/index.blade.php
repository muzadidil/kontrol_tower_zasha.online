@extends('layouts.admin')
@section('title', 'Monitoring Service')

@section('content')
@include('admin.partials._zasha-style')

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-wrench-adjustable"></i> Order Service / Teknisi</h4>
        <div class="zasha-page-subtitle">Monitoring pesanan modul service (AC, TV, kelistrikan).</div>
    </div>
</div>

<div class="zasha-card">
    <form method="GET" class="zasha-filter-bar">
        <div class="position-relative flex-grow-1" style="min-width:200px;">
            <i class="bi bi-search position-absolute" style="left:14px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:0.8rem;"></i>
            <input type="text" name="search" class="form-control ps-5" placeholder="Cari kode order..." value="{{ request('search') }}">
        </div>
        <select name="status" class="form-select" style="max-width:230px;">
            <option value="">Semua Status</option>
            @foreach(['menunggu_mitra','menuju_lokasi','diagnosa','menunggu_konfirmasi_harga','dikerjakan','menunggu_konfirmasi','selesai','ditolak','dispute'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <button class="btn btn-primary" style="background:var(--zasha-blue); border-color:var(--zasha-blue);">
            <i class="bi bi-funnel-fill me-1"></i> Filter
        </button>
        <a href="{{ route('admin.service.index') }}" class="btn btn-light">Reset</a>
    </form>

    <div class="zasha-list-header">
        <h6><i class="bi bi-list-ul"></i> Daftar Order</h6>
        <span class="badge-count">{{ $orders->total() ?? $orders->count() }}</span>
    </div>

    @forelse($orders as $order)
        @php
            $statusClass = match($order->status->value) {
                'menunggu_mitra' => 'pending',
                'menuju_lokasi', 'diagnosa', 'dikerjakan' => 'process',
                'menunggu_konfirmasi_harga' => 'pending',
                'menunggu_konfirmasi' => 'purple',
                'selesai' => 'success',
                'dispute', 'ditolak' => 'danger',
                default => 'gray',
            };
        @endphp
        <a href="{{ route('admin.service.show', $order->id) }}" class="zasha-row">
            <div class="zasha-row-icon">
                <i class="bi bi-wrench-adjustable"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    <span class="text-muted small fw-normal">{{ $order->order_code }}</span>
                    · {{ $order->pelanggan->nama_pelanggan ?? '-' }}
                </div>
                <div class="zasha-row-meta">
                    <span class="zasha-status-badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                    <span><i class="bi bi-person-badge"></i> {{ $order->mitra->nama_asli ?? '-' }}</span>
                    <span><i class="bi bi-clock"></i> {{ $order->created_at->format('d M Y') }}</span>
                </div>
            </div>
            <div class="zasha-row-amount">
                <div class="zasha-row-amount-main">
                    {{ $order->total_biaya ? 'Rp ' . number_format($order->total_biaya, 0, ',', '.') : '—' }}
                </div>
                <div class="zasha-row-amount-sub">Detail <i class="bi bi-chevron-right"></i></div>
            </div>
        </a>
    @empty
        <div class="zasha-empty">
            <i class="bi bi-wrench-adjustable"></i>
            <div class="zasha-empty-title">Belum ada order Service</div>
            <div class="zasha-empty-sub">Pesanan service akan muncul di sini.</div>
        </div>
    @endforelse

    @if(method_exists($orders, 'links'))
        <div class="p-3 border-top">{{ $orders->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
