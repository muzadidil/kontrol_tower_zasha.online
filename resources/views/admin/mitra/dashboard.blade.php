@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Dashboard Mitra</h1>
    
    <div class="mb-4">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ppobModal">PPOB / Pulsa & Listrik</button>
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#transferModal">Transfer Saldo</button>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#withdrawalModal">Tarik Dana</button>
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

<div class="modal fade" id="transferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Transfer Saldo</h5></div>
            <div class="modal-body">
                <form action="{{ route('mitra.transfer') }}" method="POST">
                    @csrf
                    <input type="text" name="receiver_id" class="form-control mb-2" placeholder="ID Mitra Penerima" required>
                    <input type="number" name="amount" class="form-control mb-2" placeholder="Jumlah" required>
                    <textarea name="description" class="form-control mb-2" placeholder="Deskripsi"></textarea>
                    <button type="submit" class="btn btn-primary">Transfer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="withdrawalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>Tarik Dana</h5></div>
            <div class="modal-body">
                <form action="{{ route('mitra.withdrawal.request') }}" method="POST">
                    @csrf
                    <input type="text" name="bank_name" class="form-control mb-2" placeholder="Nama Bank/E-Wallet" required>
                    <input type="text" name="account_number" class="form-control mb-2" placeholder="Nomor Rekening" required>
                    <input type="text" name="account_name" class="form-control mb-2" placeholder="Nama Pemilik" required>
                    <input type="number" name="nominal" class="form-control mb-2" placeholder="Nominal (Min. 50.000)" required>
                    <button type="submit" class="btn btn-primary">Request Tarik Dana</button>
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
