@extends('layouts.pelanggan')
@section('title', 'Order ' . $order->order_code)

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <a href="{{ route('pelanggan.wfh.index') }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Daftar Order
    </a>

    @foreach(['success','error','warning','info'] as $type)
        @if(session($type))
            <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
    @endforeach

    {{-- Header Status --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">Kode Order</div>
                    <div class="fw-bold fs-5">{{ $order->order_code }}</div>
                    <div class="text-muted small mt-1">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                @php
                    $badgeMap = ['pending_pembayaran'=>'secondary','menunggu_mitra'=>'warning','ditolak'=>'danger','dikerjakan'=>'info','file_terkirim'=>'info','menunggu_konfirmasi'=>'primary','dispute'=>'danger','selesai'=>'success','refund'=>'secondary'];
                @endphp
                <span class="badge bg-{{ $badgeMap[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">
                    {{ ucwords(str_replace('_', ' ', $order->status->value)) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body px-4 py-3">
            @php
                $steps = ['menunggu_mitra','dikerjakan','file_terkirim','menunggu_konfirmasi','selesai'];
                $currentStep = array_search($order->status->value, $steps);
                $currentStep = $currentStep === false ? -1 : $currentStep;
            @endphp
            <div class="d-flex justify-content-between position-relative">
                <div class="position-absolute top-50 start-0 end-0 translate-middle-y" style="height:2px;background:#dee2e6;z-index:0;margin:0 20px;"></div>
                @foreach(['Diterima','Dikerjakan','File Dikirim','Dikonfirmasi','Selesai'] as $i => $label)
                <div class="text-center position-relative" style="z-index:1;flex:1">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center fw-bold"
                        style="width:32px;height:32px;font-size:12px;
                        background:{{ $i <= $currentStep ? '#f0a500' : '#dee2e6' }};
                        color:{{ $i <= $currentStep ? '#fff' : '#6c757d' }}">
                        {{ $i < $currentStep ? '✓' : ($i + 1) }}
                    </div>
                    <div class="small mt-1 text-muted" style="font-size:10px;">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Detail Mitra --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Detail Mitra</h6>
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $order->mitra->foto_mitra ? asset('storage/' . $order->mitra->foto_mitra) : asset('images/default-avatar.png') }}"
                    class="rounded-circle" width="48" height="48" style="object-fit:cover;">
                <div>
                    <div class="fw-semibold">{{ $order->mitra->nama_asli ?? $order->mitra->nama_panggilan }}</div>
                    <div class="text-muted small">{{ $order->mitraLayanan->masterLayanan->nama_layanan ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Pekerjaan --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Detail Pekerjaan</h6>
            <div class="row g-2 mb-3">
                <div class="col-6"><div class="text-muted small">Tipe</div><div class="fw-semibold text-capitalize">{{ $order->tipe_order }}</div></div>
                <div class="col-6"><div class="text-muted small">Deadline</div><div class="fw-semibold">{{ $order->deadline_at->format('d M Y') }}</div></div>
            </div>
            <div class="mb-3">
                <div class="text-muted small mb-1">Deskripsi</div>
                <div class="bg-light rounded p-3 small">{{ $order->brief_description }}</div>
            </div>
            @if($order->reference_url)
            <div>
                <div class="text-muted small mb-1">Link Referensi</div>
                <a href="{{ $order->reference_url }}" target="_blank" class="text-warning small">
                    <i class="fas fa-external-link-alt me-1"></i>{{ Str::limit($order->reference_url, 60) }}
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- Hasil Kerja (jika sudah ada file) --}}
    @if($order->result_file_url)
    <div class="card border-0 shadow-sm border-start border-4 border-info mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-file-alt text-info me-2"></i>File Hasil Kerja</h6>
            <a href="{{ $order->result_file_url }}" target="_blank" class="btn btn-outline-info btn-sm">
                <i class="fas fa-download me-1"></i> Unduh / Buka File
            </a>
            <div class="text-muted small mt-2">Dikirim: {{ $order->file_submitted_at?->format('d M Y, H:i') }}</div>
            @if($order->auto_release_at)
                <div class="text-warning small mt-1">
                    <i class="fas fa-clock me-1"></i>
                    Auto konfirmasi pada: {{ $order->auto_release_at->format('d M Y, H:i') }}
                    ({{ $order->auto_release_at->diffForHumans() }})
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Rincian Biaya --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Rincian Biaya</h6>
            <div class="d-flex justify-content-between mb-1 small"><span>Harga Satuan</span><span>Rp {{ number_format($order->unit_price, 0, ',', '.') }}</span></div>
            @if($order->quantity != 1)
            <div class="d-flex justify-content-between mb-1 small"><span>Jumlah</span><span>× {{ $order->quantity }}</span></div>
            @endif
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold"><span>Total Dibayar</span><span class="text-warning">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
        </div>
    </div>

    {{-- Action Buttons --}}
    @if($order->status->value === 'menunggu_konfirmasi')
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2">Konfirmasi Pekerjaan</h6>
            <p class="small text-muted mb-3">Sudah puas dengan hasil kerja mitra? Klik konfirmasi untuk menyelesaikan order dan meneruskan pembayaran ke mitra.</p>
            <div class="d-flex gap-2">
                <form action="{{ route('pelanggan.wfh.konfirmasi', $order->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success" onclick="return confirm('Konfirmasi pekerjaan sudah selesai dan memuaskan?')">
                        <i class="fas fa-check me-1"></i> Konfirmasi Selesai
                    </button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#disputeModal">
                    <i class="fas fa-exclamation-triangle me-1"></i> Ada Masalah
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(in_array($order->status->value, ['ditolak','selesai','refund']))
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4 text-center">
            @if($order->status->value === 'ditolak')
                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                <p class="mb-1 fw-semibold">Order Ditolak</p>
                <p class="small text-muted">Alasan: {{ $order->alasan_penolakan }}</p>
                <p class="small text-success"><i class="fas fa-undo me-1"></i>Saldo Anda telah dikembalikan.</p>
            @elseif($order->status->value === 'selesai')
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <p class="mb-1 fw-semibold">Order Selesai</p>
                <p class="small text-muted">Diselesaikan: {{ $order->selesai_at?->format('d M Y, H:i') }}</p>
            @elseif($order->status->value === 'refund')
                <i class="fas fa-undo fa-2x text-secondary mb-2"></i>
                <p class="mb-1 fw-semibold">Refund Diproses</p>
                <p class="small text-muted">Saldo telah dikembalikan ke dompet Anda.</p>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Dispute Modal --}}
<div class="modal fade" id="disputeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Buka Dispute</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('pelanggan.wfh.dispute', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Jelaskan masalah yang Anda alami. Admin akan menghubungi Anda dan mitra untuk mediasi.</p>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Deskripsikan masalah secara detail..." required minlength="20"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Dispute</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
