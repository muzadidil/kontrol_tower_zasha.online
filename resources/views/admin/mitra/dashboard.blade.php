@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Dashboard Mitra</h1>
    
    <div class="mb-4">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ppobModal">PPOB / Pulsa & Listrik</button>
    </div>

    @foreach($orders as $order)
        @if($order->status != 'Selesai')
            <div class="card my-3">
                <div class="card-body">
                    <h3>Order #{{ $order->id }}</h3>
                    
                    @if($order->kategori == 'Jastip')
                        {{-- ... --}}
                    @elseif($order->mitra->is_wfh)
                        {{-- ... --}}
                    @else
                        <h5>Daftar Item Service:</h5>
                        <ul class="list-group mb-3">
                            @foreach($order->items as $item)
                                <li class="list-group-item">{{ $item->nama_barang }} ({{ $item->tipe_item }}) - Rp{{ number_format($item->harga_asli) }}</li>
                            @endforeach
                        </ul>
                        <form action="{{ route('mitra.tambahItemService', $order->id) }}" method="POST">
                            @csrf
                            <input type="text" name="nama_barang" placeholder="Nama Item" required>
                            <input type="number" name="harga_asli" placeholder="Harga" required>
                            <select name="tipe_item">
                                <option value="jasa">Jasa</option>
                                <option value="sparepart">Sparepart</option>
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary">Tambah Item</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    @endforeach
</div>

<div class="modal fade" id="ppobModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>PPOB</h5></div>
            <div class="modal-body">
                <form action="{{ route('ppob.transaction') }}" method="POST">
                    @csrf
                    <input type="text" name="sku" class="form-control mb-2" placeholder="SKU Produk" required>
                    <input type="text" name="target_number" class="form-control mb-2" placeholder="Nomor Tujuan" required>
                    <input type="number" name="selling_price" class="form-control mb-2" placeholder="Harga" required>
                    <button type="submit" class="btn btn-primary">Beli Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function checkLink() {
    document.getElementById('btnSelesai').disabled = document.getElementById('link_hasil_kerja').value == '';
}
</script>
@endsection
