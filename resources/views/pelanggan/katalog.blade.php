@extends('layouts.pelanggan')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .header-katalog { background: white; position: sticky; top: 0; z-index: 1020; padding: 15px 0; border-bottom: 1px solid #eee; }
    .filter-katalog { background: white; position: sticky; top: 58px; z-index: 1010; padding: 10px 0; border-bottom: 1px solid #f0f2f5; }
    .scroll-filter { overflow-x: auto; white-space: nowrap; display: flex; gap: 8px; padding: 0 15px; scrollbar-width: none; }
    .nav-pill-custom { color: #a3aed0; font-weight: 700; padding: 8px 18px; border-radius: 50px; font-size: 0.75rem; text-decoration: none; border: 1px solid #eee; transition: 0.2s; background: white; }
    .nav-pill-custom.active { background: var(--zasha-blue); color: white; border-color: var(--zasha-blue); }
    .card-mitra { border: none; border-radius: 20px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 12px; text-decoration: none; color: inherit; display: block; overflow: hidden; border: 1px solid transparent; }
    .mitra-offline { filter: grayscale(1); opacity: 0.6; }
    .img-wrapper { position: relative; width: 80px; height: 80px; flex-shrink: 0; }
    .img-mitra { width: 100%; height: 100%; object-fit: cover; border-radius: 16px; border: 2px solid #f4f7fe; }
    .badge-jarak { position: absolute; bottom: -6px; left: 50%; transform: translateX(-50%); background: var(--zasha-blue); color: white; font-size: 0.55rem; padding: 3px 8px; border-radius: 50px; border: 2px solid white; font-weight: 800; white-space: nowrap; }
    .status-badge { font-size: 0.55rem; padding: 3px 8px; border-radius: 50px; font-weight: 800; text-transform: uppercase; }
    .price-tag { color: var(--zasha-blue); font-weight: 800; font-size: 0.9rem; }
    .verified-check { color: #00a8ff; font-size: 0.85rem; }
</style>

<div class="header-katalog shadow-sm animate-in">
    <div class="container px-3 d-flex align-items-center">
        <a href="{{ route('pelanggan.dashboard') }}" class="text-dark me-3"><i class="bi bi-arrow-left fs-4"></i></a>
        <h5 class="fw-bold m-0 text-truncate" style="font-size: 1.1rem;">{{ $nama_kat }}</h5>
    </div>
</div>

<div class="filter-katalog mb-3">
    <div class="scroll-filter">
        <a href="{{ route('pelanggan.katalog', ['id_kategori' => $id_kategori, 'sort' => 'jarak']) }}" class="nav-pill-custom {{ $sort == 'jarak' ? 'active' : '' }}">
            <i class="bi bi-geo-alt-fill me-1"></i> Terdekat
        </a>
        <a href="{{ route('pelanggan.katalog', ['id_kategori' => $id_kategori, 'sort' => 'bintang']) }}" class="nav-pill-custom {{ $sort == 'bintang' ? 'active' : '' }}">
            <i class="bi bi-star-fill me-1"></i> Bintang
        </a>
        <a href="{{ route('pelanggan.katalog', ['id_kategori' => $id_kategori, 'sort' => 'orderan']) }}" class="nav-pill-custom {{ $sort == 'orderan' ? 'active' : '' }}">
            <i class="bi bi-bag-check-fill me-1"></i> Terlaris
        </a>
    </div>
</div>

<div class="container px-3">
    @forelse($mitras as $m)
        @php
            $is_online = ($is_jastip ? $m->status_mitra == 'aktif' : $m->status_mitra == 'aktif');
            $url_foto = !empty($m->foto_mitra) ? asset('img/'.$m->foto_mitra) : 'https://ui-avatars.com/api/?name='.urlencode($m->nama_mitra).'&background=002d72&color=fff';
            $target_url = $is_jastip ? route('pelanggan.detail.jastip', $m->id_mitra) : route('pelanggan.detail.mitra', $m->id_mitra);
        @endphp
        
        <a @if($is_online) href="{{ $target_url }}" @else onclick="bukaModalOffline('{{ $is_jastip ? 'Driver' : 'Mitra' }}')" @endif 
           class="card-mitra p-3 {{ $is_online ? '' : 'mitra-offline' }}" style="cursor:pointer;">
            <div class="d-flex align-items-center">
                <div class="img-wrapper me-3">
                    <img src="{{ $url_foto }}" class="img-mitra shadow-sm">
                    <span class="badge-jarak shadow-sm">{{ number_format($m->jarak, 1, ',', '.') }} km</span>
                </div>
                
                <div class="flex-grow-1 w-100 overflow-hidden">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h6 class="fw-bold m-0 text-truncate pe-2" style="font-size: 0.95rem;">
                            {{ $m->nama_mitra }} 
                            @if($is_online)<i class="bi bi-patch-check-fill verified-check ms-1"></i>@endif
                        </h6>
                        <span class="status-badge bg-{{ $is_online ? 'success' : 'secondary' }} text-white shadow-sm mt-1">
                            {{ $is_online ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                    
                    <div class="d-flex align-items-center mb-2" style="font-size: 0.7rem;">
                        <span class="bg-warning bg-opacity-10 text-warning px-2 py-1 rounded-pill fw-bold me-2">
                            <i class="bi bi-star-fill me-1"></i>{{ $m->rating_rata > 0 ? number_format($m->rating_rata, 1) : '5.0' }}
                        </span>
                        <span class="text-muted"><i class="bi bi-bag-check me-1"></i>{{ $m->total_order }} Order</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                        <div class="price-tag">
                            @if($is_jastip)
                                <span class="text-muted fw-bold" style="font-size: 0.65rem;">Mulai</span> Rp 5.000
                            @else
                                Rp {{ number_format($m->tarif_per_jam ?? 0, 0, ',', '.') }}
                                <small class="text-muted fw-normal" style="font-size: 0.65rem;">/ {{ $m->satuan_tarif ?? 'Jam' }}</small>
                            @endif
                        </div>
                        <i class="bi bi-chevron-right text-muted fs-6"></i>
                    </div>
                </div>
            </div>
        </a>
    @empty
        <div class="text-center py-5 mt-4">
            <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
            <h6 class="fw-bold text-dark mt-3">Belum ada mitra tersedia</h6>
        </div>
    @endforelse
</div>

{{-- MODAL OFFLINE --}}
<div class="modal fade" id="modalOffline" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered px-4">
        <div class="modal-content shadow-lg" style="border-radius: 25px; border: none;">
            <div class="modal-body text-center p-4">
                <i class="bi bi-moon-stars-fill text-secondary opacity-25 mb-3" style="font-size: 3.5rem; display:block;"></i>
                <h5 class="fw-bold mb-2">Sedang Istirahat</h5>
                <p class="text-muted small mb-4" id="teksOffline"></p>
                <button type="button" class="btn btn-primary w-100 rounded-pill fw-bold py-3" data-bs-dismiss="modal">OKE</button>
            </div>
        </div>
    </div>
</div>

<script>
    function bukaModalOffline(tipe) {
        document.getElementById('teksOffline').innerText = "Maaf, " + tipe + " sedang offline/istirahat. Silakan pilih yang sedang online ya!";
        var myModal = new bootstrap.Modal(document.getElementById('modalOffline'));
        myModal.show();
    }
</script>
@endsection
