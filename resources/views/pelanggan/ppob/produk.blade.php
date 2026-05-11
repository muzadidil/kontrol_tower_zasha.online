@extends('layouts.pelanggan')

@section('content')
{{-- Header --}}
<div style="background: linear-gradient(135deg, #002d72 0%, #0047b3 60%, #005dd6 100%);
            padding: 21px; position: relative; display: flex; align-items: center; gap: 13px;">
    <a href="{{ route('pelanggan.ppob.kategori', $kategori->tipe) }}" style="width: 42px; height: 42px; border-radius: 13px;
                background: rgba(255,255,255,.12); display: flex; align-items: center; justify-content: center;
                border: 1px solid rgba(255,255,255,.2); text-decoration: none; flex-shrink: 0;">
        <i class="bi bi-chevron-left" style="color: white; font-size: 18px;"></i>
    </a>
    <h4 style="color: white; font-weight: 800; font-size: 18px; margin: 0; flex: 1;">{{ $kategori->nama }}</h4>
</div>

<form action="{{ route('pelanggan.ppob.checkout') }}" method="POST" style="padding: 21px; padding-bottom: 144px;">
    @csrf

    {{-- Alert Messages --}}
    @if ($errors->any())
        <div style="background: #fee2e2; border-radius: 13px; padding: 13px 16px; margin-bottom: 13px;
                    border: 1px solid #fecaca; display: flex; gap: 13px;">
            <i class="bi bi-exclamation-triangle-fill" style="color: #dc2626; flex-shrink: 0;"></i>
            <div style="font-size: 12px; color: #991b1b;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Input Nomor Tujuan --}}
    <div style="margin-bottom: 21px;">
        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-main);
                      margin-bottom: 8px;">
            @if($kategori->tipe === 'pulsa')
                Nomor HP
            @elseif($kategori->tipe === 'token')
                ID Pelanggan PLN
            @elseif($kategori->tipe === 'game')
                User ID
            @else
                Nomor HP
            @endif
        </label>
        <input type="text" name="nomor_tujuan" class="form-control" required
               placeholder="@if($kategori->tipe === 'pulsa')08xxxxxxxxxx@elseif($kategori->tipe === 'token')Nomor meteran@elseif($kategori->tipe === 'game')User ID@else08xxxxxxxxxx@endif"
               style="border-radius: 13px; border: 1px solid #e5e7eb; padding: 13px 13px;
                      font-size: 13px; font-family: 'Courier New', monospace;">
    </div>

    {{-- Zone ID (jika server_id = 1) --}}
    @if($kategori->server_id == 1)
        <div style="margin-bottom: 21px;">
            <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-main);
                          margin-bottom: 8px;">Zone ID</label>
            <input type="text" name="zone_id" class="form-control"
                   placeholder="Zone ID (opsional)" required
                   style="border-radius: 13px; border: 1px solid #e5e7eb; padding: 13px 13px;
                          font-size: 13px; font-family: 'Courier New', monospace;">
        </div>
    @endif

    {{-- Nominal Grid --}}
    <div style="margin-bottom: 21px;">
        <label style="display: block; font-size: 12px; font-weight: 800; color: var(--text-main);
                      margin-bottom: 13px;">Pilih Nominal</label>
        <div class="row row-cols-3 g-2" style="margin-bottom: 21px;">
            @forelse($layanans as $produk)
                <div class="col">
                    <label style="cursor: pointer; display: block;">
                        <input type="radio" name="layanan_id" value="{{ $produk->id }}"
                               class="d-none" required>
                        <div class="z-card nominal-card" style="padding: 13px; text-align: center;
                                   border-radius: 13px; border: 2px solid transparent;
                                   transition: all .3s ease;">
                            <div style="font-size: 11px; font-weight: 800; color: var(--text-main);
                                       line-height: 1.3;">
                                {{ $produk->layanan }}
                            </div>
                            <div style="font-size: 13px; font-weight: 800; color: var(--blue-deep);
                                       margin-top: 4px;">
                                Rp {{ number_format($produk->harga, 0, ',', '.') }}
                            </div>
                        </div>
                    </label>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 34px 13px;
                            color: var(--text-muted); font-size: 13px;">
                    Belum ada produk tersedia.
                </div>
            @endempty
        </div>
    </div>

    {{-- Checkout Button (fixed bottom) --}}
    <div style="position: fixed; bottom: 0; left: 0; right: 0; background: white;
                border-top: 1px solid #e5e7eb; padding: 13px 21px;
                max-width: 480px; margin: 0 auto;">
        <button type="submit" class="btn-z-primary w-100" style="border-radius: 13px; padding: 13px 21px;
                font-size: 13px; font-weight: 800;">
            <i class="bi bi-check-circle me-1"></i>Lanjutkan Checkout
        </button>
    </div>
</form>

@push('styles')
<style>
input[type="radio"]:checked + .nominal-card {
    border-color: var(--blue-deep);
    background: var(--blue-pale);
}
</style>
@endpush

@endsection
