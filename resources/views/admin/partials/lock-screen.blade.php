@if(isset($activeOrder))
<div class="lock-screen-overlay">
    <div class="text-center card p-5 bg-white text-dark" style="max-width: 400px;">
        <h2 class="text-primary mb-3">Order Aktif</h2>
        <p class="mb-4">Status: <strong class="badge bg-warning text-dark">{{ $activeOrder->status }}</strong></p>
        <div class="d-grid gap-2">
            <a href="#" class="btn btn-outline-primary">Buka Maps</a>
            <a href="#" class="btn btn-outline-success">Chat WA</a>
            @if($activeOrder->status == 'Pending')
            <form action="{{ route('admin.order.updateStatus', $activeOrder->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Menuju Lokasi">
                <button type="submit" class="btn btn-primary w-100">SAYA MENUJU LOKASI</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endif