@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tracker Pesanan #{{ $order->id }}</h2>
    <div class="card mt-3">
        <div class="card-body">
            <p><strong>Status:</strong> {{ $order->status }}</p>

            @if($order->status == 'Menunggu Konfirmasi')
                @if($order->mitra->is_wfh && $order->link_hasil_kerja)
                    <div class="alert alert-info">
                        <strong>Hasil Kerja:</strong> <a href="{{ $order->link_hasil_kerja }}" target="_blank">Download/Lihat Hasil Kerja</a>
                    </div>
                @endif
                <form action="{{ route('orders.konfirmasiSelesai', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">KONFIRMASI PESANAN SELESAI</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
