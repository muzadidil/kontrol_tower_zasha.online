@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Dashboard Mitra</h1>
    
    @foreach($orders as $order)
        @if($order->status != 'Selesai')
            <div class="card my-3">
                <div class="card-body">
                    <h3>Order #{{ $order->id }}</h3>
                    
                    @if($order->kategori == 'Jastip')
                        <h5>Daftar Belanja:</h5>
                        <ul class="list-group mb-3">
                            @foreach($order->items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $item->nama_barang }} (Lokasi: {{ $item->lokasi_beli }}) - Est: Rp{{ number_format($item->harga_perkiraan) }}
                                    <form action="{{ route('mitra.updateItem', $item->id) }}" method="POST">
                                        @csrf
                                        <input type="number" name="harga_asli" value="{{ $item->harga_asli }}" placeholder="Harga Asli">
                                        <button type="submit" class="btn btn-sm btn-{{ $item->status_beli ? 'success' : 'outline-primary' }}">
                                            {{ $item->status_beli ? '✓' : 'Checklist' }}
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    @elseif($order->mitra->is_wfh)
                        <p>Tipe: WFH</p>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#briefingModal">Lihat Briefing</button>
                        
                        <form action="{{ route('mitra.updateHasilKerja', $order->id) }}" method="POST" class="mt-3">
                            @csrf
                            <input type="text" name="link_hasil_kerja" id="link_hasil_kerja" class="form-control" placeholder="URL Google Drive/DropBox" required oninput="checkLink()">
                            <button type="submit" id="btnSelesai" class="btn btn-success mt-2" disabled>SELESAI</button>
                        </form>
                    @else
                        <button class="btn btn-primary">Maps</button>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</div>

<script>
function checkLink() {
    document.getElementById('btnSelesai').disabled = document.getElementById('link_hasil_kerja').value == '';
}
</script>
@endsection
