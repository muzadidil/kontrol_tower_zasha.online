@extends('layouts.mitra')
@section('title', 'Detail ' . $order->order_code)
@section('content')
<div class="container py-4" style="max-width: 720px;">
    <a href="{{ route('mitra.service.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    @foreach(['success','error','info','warning'] as $t) @if(session($t)) <div class="alert alert-{{$t}}">{{ session($t) }}</div> @endif @endforeach
    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_lokasi'=>'info','diagnosa'=>'info','menunggu_konfirmasi_harga'=>'warning','dikerjakan'=>'info','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <div class="d-flex justify-content-between"><h5 class="fw-bold">{{ $order->order_code }}</h5>
            <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span></div>
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Pelanggan</h6>
        <div>{{ $order->pelanggan->nama_pelanggan ?? '-' }}</div>
        @if($order->pelanggan->no_wa) <a href="https://wa.me/{{ $order->pelanggan->no_wa }}" target="_blank" class="text-success small"><i class="fab fa-whatsapp"></i> {{ $order->pelanggan->no_wa }}</a> @endif
        <div class="small text-muted mt-2">{{ $order->alamat_pelanggan }}</div>
        @if($order->pelanggan_lat && $order->pelanggan_lng)<a href="https://www.google.com/maps?q={{ $order->pelanggan_lat }},{{ $order->pelanggan_lng }}" target="_blank" class="btn btn-outline-warning btn-sm mt-2"><i class="fas fa-map-marker-alt me-1"></i>Maps</a>@endif
    </div></div>

    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Keluhan</h6>
        <div class="bg-light rounded p-3 small">{{ $order->keluhan_pelanggan }}</div>
    </div></div>

    {{-- Actions --}}
    @if($order->status->value === 'menunggu_mitra')
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-2">Respons</h6>
        <div class="d-flex gap-2">
            <form action="{{ route('mitra.service.terima', $order->id) }}" method="POST">@csrf<button class="btn btn-success">Terima</button></form>
            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">Tolak</button>
        </div>
    </div></div>
    @endif

    @if($order->status->value === 'menuju_lokasi')
    <form action="{{ route('mitra.service.mulai-diagnosa', $order->id) }}" method="POST" class="mb-3">@csrf
        <button class="btn btn-info text-white w-100">Sudah Tiba — Mulai Diagnosa</button>
    </form>
    @endif

    @if($order->status->value === 'diagnosa')
    <div class="card border-0 shadow-sm border-start border-4 border-info mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Submit Hasil Diagnosa</h6>
        <form action="{{ route('mitra.service.submit-diagnosa', $order->id) }}" method="POST" id="diagForm">@csrf
            <div id="itemsContainer"></div>
            <button type="button" class="btn btn-outline-warning btn-sm mb-3" onclick="tambahItem()"><i class="fas fa-plus me-1"></i> Tambah Item</button>
            <div class="form-text small">Jasa: kena komisi 5%. Sparepart: 0% komisi.</div>
            <button type="submit" class="btn btn-success w-100">Submit Diagnosa</button>
        </form>
    </div></div>

    <template id="itemTpl">
        <div class="row g-2 mb-2 item-row">
            <div class="col-3"><select name="items[IDX][tipe]" class="form-select form-select-sm" required>
                <option value="jasa">Jasa</option><option value="sparepart">Sparepart</option></select></div>
            <div class="col-4"><input type="text" name="items[IDX][nama_item]" class="form-control form-control-sm" placeholder="Nama" required></div>
            <div class="col-3"><input type="number" name="items[IDX][harga]" class="form-control form-control-sm" placeholder="Harga" required></div>
            <div class="col-1"><input type="text" name="items[IDX][catatan]" class="form-control form-control-sm" placeholder="Catatan"></div>
            <div class="col-1"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.item-row').remove()"><i class="fas fa-times"></i></button></div>
        </div>
    </template>
    <script>
    let itemIdx = 0;
    function tambahItem() {
        const tpl = document.getElementById('itemTpl').content.cloneNode(true);
        tpl.querySelectorAll('input,select').forEach(el => el.name = el.name.replace('IDX', itemIdx));
        document.getElementById('itemsContainer').appendChild(tpl);
        itemIdx++;
    }
    document.addEventListener('DOMContentLoaded', tambahItem);
    </script>
    @endif

    @if($order->status->value === 'dikerjakan')
    <form action="{{ route('mitra.service.selesai-kerja', $order->id) }}" method="POST" class="mb-3">@csrf<button class="btn btn-success w-100">Tandai Pekerjaan Selesai</button></form>
    @endif

    {{-- Item Diagnosa Display --}}
    @if($order->orderItems->count() > 0 && $order->status->value !== 'diagnosa')
    <div class="card border-0 shadow-sm mb-3"><div class="card-body p-4">
        <h6 class="fw-bold mb-3">Item Diagnosa</h6>
        @foreach($order->orderItems as $item)
        <div class="d-flex justify-content-between small mb-1">
            <span><span class="badge bg-{{ $item->tipe === 'jasa' ? 'info' : 'secondary' }}">{{ ucfirst($item->tipe) }}</span> {{ $item->nama_item }}</span>
            <span class="fw-semibold">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div></div>
    @endif
</div>
<div class="modal fade" id="tolakModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0">
    <div class="modal-header border-0"><h5 class="fw-bold">Tolak</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('mitra.service.tolak', $order->id) }}" method="POST">@csrf
        <div class="modal-body"><textarea name="alasan" class="form-control" rows="3" minlength="10" required></textarea></div>
        <div class="modal-footer border-0"><button class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-danger">Tolak</button></div>
    </form>
</div></div></div>
@endsection
