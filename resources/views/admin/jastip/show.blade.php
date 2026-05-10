@extends('layouts.admin')
@section('title', 'Detail ' . $jastipOrder->order_code)

@section('content')
<div class="container-fluid py-4" style="max-width: 860px;">
    <a href="{{ route('admin.jastip.index') }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Monitoring Jastip
    </a>

    @foreach(['success','error'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_pickup'=>'info','belanja'=>'info','menuju_pengantaran'=>'primary','diantar'=>'primary','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="fw-bold">{{ $jastipOrder->order_code }}</h5>
                            <div class="text-muted small">{{ $jastipOrder->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <span class="badge bg-{{ $bm[$jastipOrder->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $jastipOrder->status->value)) }}</span>
                    </div>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6"><div class="text-muted small">Pelanggan</div><div class="fw-semibold">{{ $jastipOrder->pelanggan->nama_pelanggan ?? '-' }}</div></div>
                        <div class="col-md-6"><div class="text-muted small">Mitra</div><div class="fw-semibold">{{ $jastipOrder->mitra->nama_asli ?? '-' }}</div></div>
                        <div class="col-md-6"><div class="text-muted small">Total Stops</div><div class="fw-semibold">{{ $jastipOrder->total_stops }}</div></div>
                        <div class="col-md-6"><div class="text-muted small">Jarak Total</div><div class="fw-semibold">{{ $jastipOrder->total_jarak_km }} km</div></div>
                    </div>
                </div>
            </div>

            {{-- Stops --}}
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Daftar Belanja</h6>
                    @foreach($jastipOrder->stops as $stop)
                    <div class="border-start border-3 border-warning ps-3 mb-3">
                        <div class="fw-semibold">{{ $loop->iteration }}. {{ $stop->nama_lokasi }}</div>
                        <div class="text-muted small">{{ $stop->alamat_lokasi }}</div>
                        @foreach($stop->items as $item)
                        <div class="d-flex justify-content-between small mt-1">
                            <span>{{ $item->is_checked ? '✓' : '○' }} {{ $item->nama_barang }}</span>
                            <span>{{ $item->is_checked ? 'Rp ' . number_format($item->harga_asli, 0, ',', '.') : '~Rp ' . number_format($item->harga_perkiraan, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Dispute --}}
            @if($dispute)
            <div class="card border-0 shadow-sm border-start border-4 border-danger mb-3">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Dispute</h6>
                    <div class="bg-light rounded p-3 small mb-3">{{ $dispute->complaint_description }}</div>
                    @if($dispute->status === 'open')
                    <form action="{{ route('admin.jastip.resolusi', $jastipOrder->id) }}" method="POST">
                        @csrf
                        <div class="row g-2">
                            <div class="col-md-6">
                                <select name="resolusi" class="form-select" required>
                                    <option value="">Pilih Resolusi</option>
                                    <option value="release_to_mitra">✅ Mitra Menang (potong komisi)</option>
                                    <option value="refund_commission">↩️ Bebaskan Komisi (mitra tidak dipotong)</option>
                                </select>
                            </div>
                            <div class="col-12"><textarea name="admin_notes" class="form-control" rows="2" placeholder="Catatan admin"></textarea></div>
                            <div class="col-auto"><button class="btn btn-danger">Selesaikan</button></div>
                        </div>
                    </form>
                    @else
                        <span class="badge bg-success">Selesai — {{ ucwords(str_replace('_', ' ', $dispute->resolution)) }}</span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Rincian</h6>
                    <div class="d-flex justify-content-between small mb-1"><span>Belanja</span><span>Rp {{ number_format($jastipOrder->actual_total_barang ?: $jastipOrder->estimasi_total_barang, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Ongkos Jasa</span><span>Rp {{ number_format($jastipOrder->ongkos_jasa, 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small mb-1 text-warning"><span>Komisi Zasha</span><span>Rp {{ number_format($jastipOrder->komisi_zasha, 0, ',', '.') }}</span></div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between fw-bold"><span>Total COD</span><span>Rp {{ number_format($jastipOrder->grandTotalCod(), 0, ',', '.') }}</span></div>
                    <div class="d-flex justify-content-between small mt-1"><span>Pendapatan Mitra</span><span class="text-success">Rp {{ number_format($jastipOrder->pendapatan_mitra, 0, ',', '.') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
