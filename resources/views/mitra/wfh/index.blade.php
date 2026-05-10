@extends('layouts.mitra')
@section('title', 'Order WFH Masuk')

@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Order WFH</h4>

    @foreach(['success','error','info'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    {{-- Filter Tabs --}}
    <div class="d-flex gap-2 mb-4 flex-wrap">
        @foreach(['Semua','menunggu_mitra','dikerjakan','menunggu_konfirmasi','selesai','ditolak','dispute'] as $tab)
        <a href="{{ request()->fullUrlWithQuery(['status' => $tab === 'Semua' ? '' : $tab]) }}"
            class="btn btn-sm {{ (request('status', '') === ($tab === 'Semua' ? '' : $tab)) ? 'btn-warning' : 'btn-outline-secondary' }}">
            {{ ucwords(str_replace('_', ' ', $tab)) }}
        </a>
        @endforeach
    </div>

    @forelse($orders as $order)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="fw-bold text-warning">{{ $order->order_code }}</span>
                    @if($order->tipe_order === 'express')
                        <span class="badge bg-warning text-dark ms-2">⚡ Express</span>
                    @endif
                    <div class="text-muted small mt-1">{{ $order->created_at->format('d M Y, H:i') }}</div>
                    <div class="mt-2 small">{{ Str::limit($order->brief_description, 100) }}</div>
                    <div class="mt-1 text-muted small">Deadline: <strong>{{ $order->deadline_at->format('d M Y') }}</strong>
                        @if($order->status->value === 'menunggu_mitra')
                            <span class="text-danger ms-2">(Respon sebelum: {{ $order->auto_reject_at?->format('H:i') }})</span>
                        @endif
                    </div>
                </div>
                <div class="text-end">
                    @php
                        $badgeMap = ['menunggu_mitra'=>'warning','ditolak'=>'danger','dikerjakan'=>'info','file_terkirim'=>'info','menunggu_konfirmasi'=>'primary','dispute'=>'danger','selesai'=>'success','refund'=>'secondary'];
                    @endphp
                    <span class="badge bg-{{ $badgeMap[$order->status->value] ?? 'secondary' }} mb-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
                    <div class="fw-bold">Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</div>
                    <div class="text-muted small">pendapatan bersih</div>
                </div>
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="{{ route('mitra.wfh.show', $order->id) }}" class="btn btn-outline-warning btn-sm">Lihat Detail</a>
                @if($order->status->value === 'menunggu_mitra')
                    <form action="{{ route('mitra.wfh.terima', $order->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i>Terima</button>
                    </form>
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#tolakModal{{ $order->id }}">
                        <i class="fas fa-times me-1"></i>Tolak
                    </button>
                @elseif($order->status->value === 'dikerjakan')
                    <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#kirimFileModal{{ $order->id }}">
                        <i class="fas fa-paper-plane me-1"></i>Kirim File
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @if($order->status->value === 'menunggu_mitra')
    <div class="modal fade" id="tolakModal{{ $order->id }}" tabindex="-1">
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
    <div class="modal fade" id="kirimFileModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="fas fa-paper-plane text-info me-2"></i>Kirim File Hasil Kerja</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form action="{{ route('mitra.wfh.kirim-file', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label class="form-label fw-semibold">Link Google Drive / Dropbox / lainnya</label>
                        <input type="url" name="file_url" class="form-control" placeholder="https://drive.google.com/..." required>
                        <div class="form-text">Pastikan link sudah diset ke "Anyone with the link can view".</div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-info text-white"><i class="fas fa-paper-plane me-1"></i>Kirim File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    @empty
    <div class="text-center py-5"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><p class="text-muted">Tidak ada order.</p></div>
    @endforelse

    {{ $orders->links() }}
</div>
@endsection
