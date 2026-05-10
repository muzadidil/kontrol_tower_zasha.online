@extends('layouts.admin')
@section('title', 'Detail Order ' . $wfhOrder->order_code)

@section('content')
<div class="container-fluid py-4" style="max-width: 860px;">
    <a href="{{ route('admin.wfh.index') }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Monitoring WFH
    </a>

    @foreach(['success','error'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    <div class="row g-4">
        <div class="col-md-8">
            {{-- Order Info --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $wfhOrder->order_code }}
                                @if($wfhOrder->tipe_order === 'express') <span class="badge bg-warning text-dark">⚡ Express</span> @endif
                            </h5>
                            <div class="text-muted small">{{ $wfhOrder->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','dikerjakan'=>'info','file_terkirim'=>'info','menunggu_konfirmasi'=>'primary','dispute'=>'danger','selesai'=>'success','refund'=>'secondary']; @endphp
                        <span class="badge bg-{{ $bm[$wfhOrder->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $wfhOrder->status->value)) }}</span>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Pelanggan</div>
                            <div class="fw-semibold">{{ $wfhOrder->pelanggan->nama_pelanggan ?? '-' }}</div>
                            <div class="small text-muted">{{ $wfhOrder->pelanggan->no_wa ?? '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Mitra</div>
                            <div class="fw-semibold">{{ $wfhOrder->mitra->nama_asli ?? $wfhOrder->mitra->nama_panggilan ?? '-' }}</div>
                            <div class="small text-muted">{{ $wfhOrder->mitra->no_wa ?? '' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Layanan</div>
                            <div class="fw-semibold">{{ $wfhOrder->mitraLayanan->masterLayanan->nama_layanan ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small mb-1">Deadline</div>
                            <div class="fw-semibold">{{ $wfhOrder->deadline_at->format('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="text-muted small mb-1">Deskripsi</div>
                        <div class="bg-light rounded p-3 small">{{ $wfhOrder->brief_description }}</div>
                    </div>
                    @if($wfhOrder->reference_url)
                    <div class="mt-2">
                        <a href="{{ $wfhOrder->reference_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i> Referensi
                        </a>
                    </div>
                    @endif
                    @if($wfhOrder->result_file_url)
                    <div class="mt-3">
                        <div class="text-muted small mb-1">File Hasil Kerja</div>
                        <a href="{{ $wfhOrder->result_file_url }}" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-file me-1"></i> Buka File
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Dispute Section --}}
            @if($dispute)
            <div class="card border-0 shadow-sm border-start border-4 border-danger mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Dispute Aktif</h6>
                    <div class="bg-light rounded p-3 small mb-3">{{ $dispute->complaint_description }}</div>
                    <div class="text-muted small mb-3">Diajukan oleh: <strong>{{ ucfirst($dispute->raised_by_type) }}</strong> — {{ $dispute->created_at->format('d M Y, H:i') }}</div>

                    @if($dispute->status === 'open' && $wfhOrder->status->value === 'dispute')
                    <form action="{{ route('admin.wfh.resolusi', $wfhOrder->id) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select name="resolusi" class="form-select" required>
                                    <option value="">Pilih Resolusi</option>
                                    <option value="release_to_mitra">✅ Cairkan ke Mitra (mitra menang)</option>
                                    <option value="refund_to_customer">↩️ Refund ke Pelanggan</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <textarea name="admin_notes" class="form-control" rows="2" placeholder="Catatan admin (opsional)"></textarea>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-danger">Selesaikan Dispute</button>
                            </div>
                        </div>
                    </form>
                    @else
                        <span class="badge bg-success">Dispute Selesai — {{ ucwords(str_replace('_', ' ', $dispute->resolution ?? '')) }}</span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            {{-- Rincian Keuangan --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Rincian Keuangan</h6>
                    <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Total Dibayar</span><span class="fw-semibold">Rp {{ number_format($wfhOrder->total_price, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Komisi Zasha (5%)</span><span class="fw-semibold text-warning">Rp {{ number_format($wfhOrder->komisi_zasha, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small"><span class="text-muted">Pendapatan Mitra</span><span class="fw-semibold text-success">Rp {{ number_format($wfhOrder->pendapatan_mitra, 0, ',', '.') }}</span></div>
                </div>
            </div>

            {{-- Escrow Ledger --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Riwayat Escrow</h6>
                    @forelse($wfhOrder->escrowLedgers as $ledger)
                    <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                        <div>
                            <div class="small fw-semibold text-capitalize">{{ str_replace('_', ' ', $ledger->type) }}</div>
                            <div class="small text-muted">{{ $ledger->keterangan }}</div>
                            <div class="small text-muted">{{ $ledger->created_at->format('d M Y H:i') }}</div>
                        </div>
                        <div class="small fw-bold {{ in_array($ledger->type, ['hold']) ? 'text-danger' : 'text-success' }}">
                            Rp {{ number_format($ledger->amount, 0, ',', '.') }}
                        </div>
                    </div>
                    @empty
                    <div class="text-muted small">Belum ada transaksi escrow.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
