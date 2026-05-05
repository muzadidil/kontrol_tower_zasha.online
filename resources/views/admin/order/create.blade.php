@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Buat Order Baru</h2>
    <form action="{{ route('admin.order.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Pelanggan</label>
            <select name="pelanggan_id" class="form-control" required>
                @foreach($pelanggans as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_lengkap }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Mitra</label>
            <select name="mitra_id" class="form-control" required>
                @foreach($mitras as $m)
                    <option value="{{ $m->id }}">{{ $m->nama_panggilan }} (Tarif: {{ $m->tarif_per_jam }}/jam)</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Tipe Waktu</label>
            <select name="tipe_waktu" class="form-control">
                <option value="Instan">Instan</option>
                <option value="Terjadwal">Terjadwal</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Durasi Kerja</label>
            <input type="number" name="durasi_kerja" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Metode Pembayaran</label>
            <select name="metode_pembayaran" class="form-control">
                <option value="COD">COD</option>
                <option value="Transfer">Transfer</option>
                <option value="Saldo">Saldo</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Kategori</label>
            <select name="kategori" id="kategori" class="form-control" onchange="toggleJastip()">
                <option value="Reguler">Reguler</option>
                <option value="Jastip">Jastip</option>
            </select>
        </div>
        
        <div id="jastipSection" style="display:none;">
            <h4>Daftar Belanja Jastip</h4>
            <div id="itemsContainer">
                <div class="row mb-2 item-row">
                    <div class="col"><input type="text" name="items[0][nama]" class="form-control" placeholder="Nama Barang"></div>
                    <div class="col"><input type="number" name="items[0][harga]" class="form-control" placeholder="Harga Perkiraan"></div>
                    <div class="col"><input type="text" name="items[0][lokasi]" class="form-control" placeholder="Lokasi Beli"></div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="addItem()">+ Tambah Barang</button>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Order</button>
    </form>
</div>

<script>
function toggleJastip() {
    document.getElementById('jastipSection').style.display = document.getElementById('kategori').value == 'Jastip' ? 'block' : 'none';
}
function addItem() {
    let container = document.getElementById('itemsContainer');
    let index = container.children.length;
    let div = document.createElement('div');
    div.className = 'row mb-2 item-row';
    div.innerHTML = `<div class="col"><input type="text" name="items[${index}][nama]" class="form-control" placeholder="Nama Barang"></div>` +
                    `<div class="col"><input type="number" name="items[${index}][harga]" class="form-control" placeholder="Harga Perkiraan"></div>` +
                    `<div class="col"><input type="text" name="items[${index}][lokasi]" class="form-control" placeholder="Lokasi Beli"></div>`;
    container.appendChild(div);
}
</script>
@endsection
