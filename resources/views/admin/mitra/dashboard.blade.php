@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Dashboard Mitra</h1>
    
    @foreach($orders as $order)
        @if($order->status != 'Selesai')
            <div class="card my-3">
                <div class="card-body">
                    <h3>Order #{{ $order->id }}</h3>
                    
                    @if($order->mitra->is_wfh)
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
