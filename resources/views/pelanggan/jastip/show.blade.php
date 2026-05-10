@extends('layouts.pelanggan')
@section('title', 'Detail ' . $order->order_code)

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <a href="{{ route('pelanggan.jastip.index') }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Daftar Jastip
    </a>

    @foreach(['success','error','warning'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_pickup'=>'info','belanja'=>'info','menuju_pengantaran'=>'primary','diantar'=>'primary','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    {{-- Header --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Kode Order</div>
                    <div class="fw-bold fs-5">{{ $order->order_code }}</div>
                </div>
                <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">
                    {{ ucwords(str_replace('_', ' ', $order->status->value)) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Mitra --}}
    @if($order->mitra)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4 d-flex align-items-center gap-3">
            <i class="fas fa-motorcycle fa-2x text-warning"></i>
            <div class="flex-grow-1">
                <div class="fw-semibold">{{ $order->mitra->nama_asli ?? $order->mitra->nama_panggilan }}</div>
                @if($order->mitra->no_wa)
                    <a href="https://wa.me/{{ $order->mitra->no_wa }}" target="_blank" class="text-success small">
                        <i class="fab fa-whatsapp me-1"></i>{{ $order->mitra->no_wa }}
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Stops & Items --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Daftar Belanja ({{ $order->stops->count() }} lokasi)</h6>
            @foreach($order->stops as $stop)
            <div class="border-start border-3 border-warning ps-3 mb-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong class="small">{{ $loop->iteration }}. {{ $stop->nama_lokasi }}</strong>
                        <div class="text-muted small">{{ $stop->alamat_lokasi }}</div>
                    </div>
                    @if($stop->tiba_at)
                        <span class="badge bg-success">✓ Tiba</span>
                    @endif
                </div>
                <div class="mt-2">
                    @foreach($stop->items as $item)
                    <div class="d-flex justify-content-between small mb-1 {{ $item->is_checked ? 'text-success' : '' }}">
                        <span>
                            @if($item->is_checked) <i class="fas fa-check-circle"></i> @else <i class="far fa-circle text-muted"></i> @endif
                            {{ $item->nama_barang }}
                        </span>
                        <span>
                            @if($item->is_checked)
                                <strong>Rp {{ number_format($item->harga_asli, 0, ',', '.') }}</strong>
                            @else
                                <span class="text-muted">~Rp {{ number_format($item->harga_perkiraan, 0, ',', '.') }}</span>
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pengantaran --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-home text-warning me-2"></i>Diantar ke</h6>
            <div class="small">{{ $order->delivery_address }}</div>
        </div>
    </div>

    {{-- Rincian Biaya --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Rincian Biaya</h6>
            <div class="d-flex justify-content-between small mb-1">
                <span>Total Belanja {{ $order->actual_total_barang > 0 ? '(aktual)' : '(estimasi)' }}</span>
                <span class="fw-semibold">Rp {{ number_format($order->actual_total_barang > 0 ? $order->actual_total_barang : $order->estimasi_total_barang, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between small mb-1">
                <span>Ongkos Jasa ({{ $order->total_jarak_km }} km · {{ $order->total_stops }} stops)</span>
                <span class="fw-semibold">Rp {{ number_format($order->ongkos_jasa, 0, ',', '.') }}</span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold">
                <span>Bayar saat Diantar (COD)</span>
                <span class="text-warning">Rp {{ number_format($order->grandTotalCod(), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($order->status->value === 'menunggu_konfirmasi')
    <div class="card border-0 shadow-sm border-start border-4 border-warning">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2">Konfirmasi Penerimaan</h6>
            <p class="small text-muted mb-3">Sudah terima barang dan bayar mitra? Klik konfirmasi.</p>
            <div class="d-flex gap-2">
                <form action="{{ route('pelanggan.jastip.konfirmasi', $order->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success" onclick="return confirm('Konfirmasi barang diterima?')">
                        <i class="fas fa-check me-1"></i> Konfirmasi
                    </button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal">
                    <i class="fas fa-exclamation-triangle me-1"></i> Ada Masalah
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Dispute Modal --}}
<div class="modal fade" id="disputeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold text-danger">Buka Dispute</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('pelanggan.jastip.dispute', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan masalah..." required minlength="20"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
