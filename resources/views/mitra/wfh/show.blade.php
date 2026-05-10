@extends('layouts.mitra')
@section('title', 'Order ' . $order->order_code)

@section('content')
<div class="container py-4" style="max-width: 680px;">
    <a href="{{ route('mitra.wfh.index') }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Daftar Order
    </a>

    @foreach(['success','error','info','warning'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="text-muted small">Kode Order</div>
                    <div class="fw-bold fs-5">{{ $order->order_code }}
                        @if($order->tipe_order === 'express') <span class="badge bg-warning text-dark ms-1">⚡ Express</span> @endif
                    </div>
                    <div class="text-muted small mt-1">{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                @php $badgeMap = ['menunggu_mitra'=>'warning','ditolak'=>'danger','dikerjakan'=>'info','file_terkirim'=>'info','menunggu_konfirmasi'=>'primary','dispute'=>'danger','selesai'=>'success','refund'=>'secondary']; @endphp
                <span class="badge bg-{{ $badgeMap[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
            </div>
        </div>
    </div>

    {{-- Info Pelanggan --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Info Pelanggan</h6>
            <div class="d-flex align-items-center gap-3">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                    <i class="fas fa-user text-muted"></i>
                </div>
                <div>
                    <div class="fw-semibold">{{ $order->pelanggan->nama_pelanggan ?? 'Pelanggan' }}</div>
                    @if($order->pelanggan->no_wa)
                    <a href="https://wa.me/{{ $order->pelanggan->no_wa }}" target="_blank" class="text-success small">
                        <i class="fab fa-whatsapp me-1"></i>{{ $order->pelanggan->no_wa }}
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Pekerjaan --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Detail Pekerjaan</h6>
            <div class="row g-2 mb-3">
                <div class="col-6"><div class="text-muted small">Deadline</div><div class="fw-semibold">{{ $order->deadline_at->format('d M Y') }}</div></div>
                <div class="col-6"><div class="text-muted small">Pendapatan</div><div class="fw-semibold text-success">Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</div></div>
            </div>
            <div class="bg-light rounded p-3 small mb-3">{{ $order->brief_description }}</div>
            @if($order->reference_url)
            <a href="{{ $order->reference_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i> Lihat Referensi
            </a>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    @if($order->status->value === 'menunggu_mitra')
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-1">Respons Diperlukan</h6>
            <p class="small text-danger mb-3"><i class="fas fa-clock me-1"></i>Batas respon: {{ $order->auto_reject_at?->format('H:i') }} ({{ $order->auto_reject_at?->diffForHumans() }})</p>
            <div class="d-flex gap-2">
                <form action="{{ route('mitra.wfh.terima', $order->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success"><i class="fas fa-check me-1"></i>Terima Order</button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                    <i class="fas fa-times me-1"></i>Tolak
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($order->status->value === 'dikerjakan')
    <div class="card border-0 shadow-sm border-start border-4 border-info mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-hammer text-info me-2"></i>Sedang Dikerjakan</h6>
            <p class="small text-muted mb-3">Selesaikan pekerjaan sebelum deadline, lalu kirimkan link file hasil kerja.</p>
            <button class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#kirimFileModal">
                <i class="fas fa-paper-plane me-1"></i>Kirim File Hasil Kerja
            </button>
        </div>
    </div>
    @endif

    @if($order->result_file_url)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2">File yang Dikirim</h6>
            <a href="{{ $order->result_file_url }}" target="_blank" class="btn btn-outline-info btn-sm">
                <i class="fas fa-external-link-alt me-1"></i> Buka File
            </a>
            <div class="text-muted small mt-2">Dikirim: {{ $order->file_submitted_at?->format('d M Y, H:i') }}</div>
        </div>
    </div>
    @endif
</div>

{{-- Modals --}}
@if($order->status->value === 'menunggu_mitra')
<div class="modal fade" id="tolakModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold">Tolak Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('mitra.wfh.tolak', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <textarea name="alasan" class="form-control" rows="3" placeholder="Alasan penolakan..." required minlength="10"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($order->status->value === 'dikerjakan')
<div class="modal fade" id="kirimFileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="fas fa-paper-plane text-info me-2"></i>Kirim File</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('mitra.wfh.kirim-file', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="url" name="file_url" class="form-control" placeholder="https://drive.google.com/..." required>
                    <div class="form-text mt-1">Pastikan link bisa diakses publik (Anyone with the link).</div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-info text-white"><i class="fas fa-paper-plane me-1"></i>Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
