@extends('layouts.admin')
@section('title', 'Detail ' . $tenagaOrder->order_code)
@section('content')
<div class="container-fluid py-4" style="max-width: 860px;">
    <a href="{{ route('admin.tenaga.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    @foreach(['success','error'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
                <div class="d-flex justify-content-between"><h5 class="fw-bold">{{ $tenagaOrder->order_code }}</h5><span class="badge bg-{{ $bm[$tenagaOrder->status->value] ?? 'secondary' }}">{{ ucwords(str_replace('_', ' ', $tenagaOrder->status->value)) }}</span></div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6"><div class="text-muted small">Pelanggan</div><div>{{ $tenagaOrder->pelanggan->nama_pelanggan ?? '-' }}</div></div>
                    <div class="col-md-6"><div class="text-muted small">Mitra</div><div>{{ $tenagaOrder->mitra->nama_asli ?? '-' }}</div></div>
                    <div class="col-md-6"><div class="text-muted small">Durasi</div><div>{{ $tenagaOrder->durasi }} {{ $tenagaOrder->tipe_durasi }}</div></div>
                    <div class="col-md-6"><div class="text-muted small">Pembayaran</div><div>{{ strtoupper($tenagaOrder->metode_pembayaran) }}</div></div>
                </div>
            </div></div>
            @if($dispute)
            <div class="card border-0 shadow-sm border-start border-4 border-danger"><div class="card-body p-4">
                <h6 class="fw-bold text-danger">Dispute</h6>
                <div class="bg-light rounded p-3 small mb-3">{{ $dispute->complaint_description }}</div>
                @if($dispute->status === 'open')
                <form action="{{ route('admin.tenaga.resolusi', $tenagaOrder->id) }}" method="POST">@csrf
                    <select name="resolusi" class="form-select mb-2" required>
                        <option value="">Pilih Resolusi</option>
                        <option value="release_to_mitra">Mitra Menang</option>
                        <option value="refund_to_customer">Refund Pelanggan</option>
                    </select>
                    <textarea name="admin_notes" class="form-control mb-2" rows="2" placeholder="Catatan"></textarea>
                    <button class="btn btn-danger">Selesaikan</button>
                </form>
                @endif
            </div></div>
            @endif
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm"><div class="card-body p-4">
                <h6 class="fw-bold mb-3">Rincian</h6>
                <div class="d-flex justify-content-between small mb-1"><span>Total</span><span>Rp {{ number_format($tenagaOrder->total_biaya, 0, ',', '.') }}</span></div>
                <div class="d-flex justify-content-between small mb-1 text-warning"><span>Komisi</span><span>Rp {{ number_format($tenagaOrder->komisi_zasha, 0, ',', '.') }}</span></div>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold text-success"><span>Pendapatan Mitra</span><span>Rp {{ number_format($tenagaOrder->pendapatan_mitra, 0, ',', '.') }}</span></div>
            </div></div>
        </div>
    </div>
</div>
@endsection
