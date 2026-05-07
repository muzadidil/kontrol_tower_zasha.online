@extends('layouts.pelanggan')

@section('content')
@php
    if (!function_exists('getBadgeColor')) {
        function getBadgeColor($status) {
            $s = strtolower(trim($status));
            if (in_array($s, ['menunggu', 'pending'])) return 'bg-warning text-dark';
            if (in_array($s, ['proses', 'berjalan', 'aktif', 'diterima', 'belanja', 'pengiriman'])) return 'bg-primary text-white';
            if ($s == 'selesai') return 'bg-success text-white';
            return 'bg-secondary text-white';
        }
    }
@endphp

<style>
    :root { --zasha-blue: #002d72; }
    body { background-color: #f4f7fe; color: #2b3674; padding-bottom: 100px; }
    .header-top { background: white; border-bottom: 1px solid #eee; padding: 20px; position: sticky; top: 0; z-index: 1000; }
    .scroll-nav { overflow-x: auto; white-space: nowrap; padding: 12px 15px; background: white; border-bottom: 1px solid #eee; display: flex; gap: 8px; scrollbar-width: none; }
    .scroll-nav::-webkit-scrollbar { display: none; }
    .nav-pill-custom { color: #a3aed0; font-weight: 700; padding: 8px 18px; border-radius: 50px; font-size: 0.75rem; text-decoration: none; border: 1px solid #eee; transition: 0.3s; background: white; }
    .nav-pill-custom.active { background: var(--zasha-blue); color: white; border-color: var(--zasha-blue); }
    .card-riwayat { border: none; border-radius: 20px; background: white; box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 15px; padding: 18px; position: relative; }
    .img-mitra { width: 50px; height: 50px; border-radius: 12px; object-fit: cover; }
    .label-type { font-size: 0.6rem; font-weight: 800; padding: 3px 10px; border-radius: 5px; text-transform: uppercase; margin-bottom: 8px; display: inline-block; }
    .label-jastip { background: #e0f2fe; color: #0369a1; }
    .label-jasa { background: #f1f5f9; color: #475569; }
    .status-badge { font-size: 0.65rem; font-weight: 800; border-radius: 50px; padding: 5px 12px; }
    .star-rating { color: #eee; cursor: pointer; font-size: 2.2rem; transition: 0.2s; }
    .star-rating.active { color: #ffc107; }
</style>

<div id="main-content">
    <div class="header-top text-center shadow-sm">
        <h5 class="fw-bold m-0">Riwayat Pesanan</h5>
    </div>

    <div class="scroll-nav shadow-sm mb-3">
        @foreach(['semua', 'Menunggu', 'Proses', 'Selesai'] as $nav_status)
            <a href="{{ route('pelanggan.riwayat', ['status' => $nav_status]) }}" 
               class="nav-pill-custom {{ (strtolower($status_filter) == strtolower($nav_status)) ? 'active' : '' }}">
                {{ $nav_status == 'Proses' ? 'Berjalan' : $nav_status }}
            </a>
        @endforeach
    </div>

    <div class="container px-3">
        {{-- Loop Jastip --}}
        @foreach($query_jastip as $rj)
            @php $st_j = strtolower(trim($rj->status_jastip)); @endphp
            <div class="card card-riwayat border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="label-type label-jastip">JASTIP HUNTER</span>
                        <h6 class="fw-bold m-0 small">#{{ $rj->id_jastip }}</h6>
                    </div>
                    <span class="badge status-badge {{ getBadgeColor($rj->status_jastip) }}">{{ strtoupper($rj->status_jastip) }}</span>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ !empty($rj->foto_driver) ? asset('img/'.$rj->foto_driver) : 'https://ui-avatars.com/api/?name='.urlencode($rj->nama_driver).'&background=002d72&color=fff' }}" class="img-mitra me-3 shadow-sm">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0 small">{{ $rj->nama_driver }}</h6>
                        <small class="text-muted d-block text-truncate" style="max-width: 180px;"><i class="bi bi-shop me-1"></i>{{ $rj->lokasi_asal }}</small>
                    </div>
                    <a href="#" class="btn btn-light btn-sm rounded-pill px-3 fw-bold border" style="font-size: 0.7rem;">Detail</a>
                </div>

                <div class="border-top pt-3 mt-1">
                    @if(in_array($st_j, ['diterima', 'belanja', 'pengiriman']))
                        <a href="{{ route('pelanggan.riwayat.update', ['aksi' => 'selesai', 'type' => 'jastip', 'id_order' => $rj->id_jastip]) }}" 
                           class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm" 
                           onclick="return confirm('Pesanan jastip sudah diterima?')">KONFIRMASI DITERIMA</a>
                    @elseif($st_j == 'selesai')
                        <div class="text-center text-success small fw-bold py-1"><i class="bi bi-check-all me-1"></i> Pesanan Selesai</div>
                    @else
                        <div class="text-center text-muted small py-1">{{ $rj->status_jastip }}</div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Loop Jasa --}}
        @foreach($query_jasa as $r)
            @php $st = strtolower(trim($r->status_pesanan)); @endphp
            <div class="card card-riwayat">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="label-type label-jasa">LAYANAN JASA</span>
                        <h6 class="fw-bold m-0 small">ID #{{ $r->id_pesanan }}</h6>
                    </div>
                    <span class="badge status-badge {{ getBadgeColor($r->status_pesanan) }}">{{ strtoupper($r->status_pesanan) }}</span>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ !empty($r->foto_mitra) ? asset('img/'.$r->foto_mitra) : 'https://ui-avatars.com/api/?name='.urlencode($r->nama_mitra).'&background=64748b&color=fff' }}" class="img-mitra me-3 shadow-sm">
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0 small">{{ $r->nama_mitra }}</h6>
                        <small class="text-muted d-block"><i class="bi bi-tools me-1"></i>{{ $r->nama_kategori }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-primary small">Rp {{ number_format($r->total_pesanan, 0, ',', '.') }}</div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-1">
                    @if($st == 'menunggu' || $st == 'pending')
                        <a href="{{ route('pelanggan.riwayat.update', ['aksi' => 'batal', 'id_order' => $r->id_pesanan]) }}" 
                           class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold" 
                           onclick="return confirm('Batalkan pesanan ini?')">BATALKAN PESANAN</a>
                    @elseif($st == 'selesai')
                        @if($r->rating == 0)
                            <button type="button" class="btn btn-warning btn-sm w-100 rounded-pill fw-bold text-dark py-2 shadow-sm" 
                                    onclick="bukaRating('{{ $r->id_pesanan }}', '{{ $r->nama_mitra }}')">
                                <i class="bi bi-star-fill me-1"></i> BERI PENILAIAN
                            </button>
                        @else
                            <div class="text-center text-success small fw-bold py-1"><i class="bi bi-patch-check-fill me-1"></i> Penilaian Sudah Dikirim</div>
                        @endif
                    @else
                        <a href="{{ route('pelanggan.riwayat.update', ['aksi' => 'selesai', 'id_order' => $r->id_pesanan]) }}" 
                           class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm" 
                           onclick="return confirm('Pekerjaan sudah selesai?')">KONFIRMASI SELESAI</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- MODAL RATING --}}
<div class="modal fade" id="modalRating" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 25px; border:none;">
            <div class="modal-body p-4 text-center">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle d-inline-block mb-3">
                    <i class="bi bi-star-fill fs-2"></i>
                </div>
                <h5 class="fw-bold mb-1">Kasih Nilai Jasa</h5>
                <p class="text-muted small mb-4" id="namaMitraReview"></p>
                <form action="{{ route('pelanggan.riwayat.ulasan') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_order" id="idOrderReview">
                    <input type="hidden" name="rating_nilai" id="ratingNilai" value="5">
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star-fill star-rating active" data-value="{{ $i }}" onclick="setStar({{ $i }})"></i>
                        @endfor
                    </div>
                    <textarea name="ulasan_teks" class="form-control rounded-4 mb-4" rows="3" placeholder="Tulis komentar kamu di sini..." style="background: #f4f7fe; border:none;"></textarea>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100 rounded-pill fw-bold py-3" data-bs-dismiss="modal">BATAL</button>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow">KIRIM</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const ratingModal = new bootstrap.Modal(document.getElementById('modalRating'));

    function bukaRating(id, nama) {
        document.getElementById('idOrderReview').value = id;
        document.getElementById('namaMitraReview').innerText = 'Bagaimana kepuasan kamu terhadap ' + nama + '?';
        ratingModal.show();
    }

    function setStar(val) {
        document.getElementById('ratingNilai').value = val;
        const stars = document.querySelectorAll('.star-rating');
        stars.forEach((s, index) => {
            if (index < val) s.classList.add('active');
            else s.classList.remove('active');
        });
    }
</script>
@endsection