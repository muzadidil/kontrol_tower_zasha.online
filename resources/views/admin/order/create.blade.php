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
        <button type="submit" class="btn btn-primary">Simpan Order</button>
    </form>
</div>
@endsection
