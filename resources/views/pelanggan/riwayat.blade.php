@extends('layouts.pelanggan')

@push('styles')
<style>
    .scroll-filter { display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;padding:13px 21px; }
    .scroll-filter::-webkit-scrollbar { display:none; }
    .filter-pill {
        white-space:nowrap; border-radius:34px; padding:8px 18px; font-size:12px; font-weight:700;
        text-decoration:none; border:1.5px solid var(--border);
        background:var(--surface); color:var(--text-muted); transition:.15s; flex-shrink:0;
    }
    .filter-pill.active { background:var(--blue-deep); color:white; border-color:var(--blue-deep); }
    .order-card {
        background:var(--surface); border-radius:21px; padding:21px;
        margin:0 21px 13px; box-shadow:0 2px 12px rgba(0,45,114,.06);
    }
    .tag-jastip { background:#e0f2fe; color:#036991; }
    .tag-jasa   { background:#eff6ff; color:#002d72; }
    .order-tag  { font-size:9px;font-weight:800;border-radius:5px;padding:3px 10px;
                  text-transform:uppercase;letter-spacing:.4px;display:inline-block; }
    .mitra-ava  { width:44px;height:44px;border-radius:13px;object-fit:cover; }
    .star-rating { color:#e2e8f0; cursor:pointer; font-size:2.2rem; transition:.2s; }
    .star-rating.active { color:#f0a500; }
</style>
@endpush

@section('content')

{{-- ── HEADER ────────────────────────────────────── --}}
<div style="background:linear-gradient(145deg,#001d4d,#002d72);padding:21px 21px 13px;
            position:sticky;top:0;z-index:100;">
    <div style="font-size:18px;font-weight:800;color:white;text-align:center;">Riwayat Pesanan</div>
</div>

{{-- ── FILTER PILLS ────────────────────────────── --}}
<div style="background:var(--surface);border-bottom:1px solid var(--border);position:sticky;top:63px;z-index:99;">
    <div class="scroll-filter">
        @foreach(['semua'=>'Semua', 'Menunggu'=>'Menunggu', 'Proses'=>'Berjalan', 'Selesai'=>'Selesai'] as $val => $label)
            <a href="{{ route('pelanggan.riwayat.index', ['status' => $val]) }}"
               class="filter-pill {{ strtolower($status_filter) == strtolower($val) ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- ── ORDERS ───────────────────────────────────── --}}
<div style="padding-top:13px;padding-bottom:8px;">

    {{-- Jastip --}}
    @foreach($query_jastip as $rj)
        @php $st_j = strtolower(trim($rj->status_jastip)); @endphp
        <div class="order-card" style="border-left:4px solid var(--cyan);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:13px;">
                <div>
                    <span class="order-tag tag-jastip">Jastip Hunter</span>
                    <div style="font-size:13px;font-weight:800;color:var(--text-main);margin-top:5px;">
                        #{{ $rj->id_jastip }}
                    </div>
                </div>
                @php
                    $sc = ['menunggu'=>'background:#fef9c3;color:#b45309;','mencari driver'=>'background:#fef9c3;color:#b45309;',
                           'diterima'=>'background:#dbeafe;color:#1e40af;','belanja'=>'background:#dbeafe;color:#1e40af;',
                           'pengiriman'=>'background:#dbeafe;color:#1e40af;','selesai'=>'background:#d1fae5;color:#065f46;'];
                    $sc2 = $sc[$st_j] ?? 'background:#f1f5f9;color:#475569;';
                @endphp
                <span style="{{ $sc2 }}border-radius:34px;font-size:9px;font-weight:800;padding:5px 12px;text-transform:uppercase;">
                    {{ $rj->status_jastip }}
                </span>
            </div>

            <div style="display:flex;align-items:center;gap:13px;margin-bottom:13px;">
                <img src="{{ !empty($rj->foto_driver)
                        ? asset('storage/'.$rj->foto_driver)
                        : 'https://ui-avatars.com/api/?name='.urlencode($rj->nama_driver ?? 'D').'&background=00b4d8&color=fff&bold=true' }}"
                     class="mitra-ava">
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:700;color:var(--text-main);">{{ $rj->nama_driver ?? 'Driver' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">
                        <i class="bi bi-shop me-1"></i>{{ $rj->lokasi_asal ?? '-' }}
                    </div>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:13px;">
                @if(in_array($st_j, ['diterima','belanja','pengiriman']))
                    <a href="{{ route('pelanggan.riwayat.update', ['aksi'=>'selesai','type'=>'jastip','id_order'=>$rj->id_jastip]) }}"
                       class="btn-z-primary w-100 d-block text-center text-decoration-none" style="padding:12px;"
                       onclick="return confirm('Pesanan jastip sudah kamu terima?')">
                        KONFIRMASI DITERIMA
                    </a>
                @elseif($st_j == 'selesai')
                    <div style="text-align:center;color:var(--green);font-size:13px;font-weight:700;padding:5px;">
                        <i class="bi bi-check-all me-1"></i>Pesanan Selesai
                    </div>
                @else
                    <div style="text-align:center;color:var(--text-faint);font-size:12px;padding:5px;">
                        {{ $rj->status_jastip }}
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Jasa --}}
    @foreach($query_jasa as $r)
        @php $st = strtolower(trim($r->status_pesanan)); @endphp
        <div class="order-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:13px;">
                <div>
                    <span class="order-tag tag-jasa">Layanan Jasa</span>
                    <div style="font-size:13px;font-weight:800;color:var(--text-main);margin-top:5px;">
                        ID #{{ $r->id_pesanan }}
                    </div>
                </div>
                @php
                    $js = ['menunggu'=>'background:#fef9c3;color:#b45309;','pending'=>'background:#fef9c3;color:#b45309;',
                           'proses'=>'background:#dbeafe;color:#1e40af;','berjalan'=>'background:#dbeafe;color:#1e40af;',
                           'selesai'=>'background:#d1fae5;color:#065f46;','batal'=>'background:#fee2e2;color:#991b1b;',
                           'dibatalkan'=>'background:#fee2e2;color:#991b1b;'];
                    $js2 = $js[$st] ?? 'background:#f1f5f9;color:#475569;';
                @endphp
                <span style="{{ $js2 }}border-radius:34px;font-size:9px;font-weight:800;padding:5px 12px;text-transform:uppercase;">
                    {{ $r->status_pesanan }}
                </span>
            </div>

            <div style="display:flex;align-items:center;gap:13px;margin-bottom:13px;">
                <img src="{{ !empty($r->foto_mitra)
                        ? asset('img/'.$r->foto_mitra)
                        : 'https://ui-avatars.com/api/?name='.urlencode($r->nama_mitra ?? 'M').'&background=002d72&color=fff&bold=true' }}"
                     class="mitra-ava">
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:700;color:var(--text-main);">{{ $r->nama_mitra ?? 'Mitra' }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">
                        <i class="bi bi-tools me-1"></i>{{ $r->nama_kategori ?? '-' }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:15px;font-weight:800;color:#002d72;">
                        Rp {{ number_format($r->total_pesanan, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div style="border-top:1px solid var(--border);padding-top:13px;">
                @if(in_array($st, ['menunggu','pending']))
                    <a href="{{ route('pelanggan.riwayat.update', ['aksi'=>'batal','id_order'=>$r->id_pesanan]) }}"
                       style="display:block;width:100%;text-align:center;border:1.5px solid #fca5a5;
                              color:#ef4444;border-radius:34px;padding:10px;font-size:12px;font-weight:800;
                              text-decoration:none;"
                       onclick="return confirm('Batalkan pesanan ini?')">
                        BATALKAN PESANAN
                    </a>
                @elseif($st == 'selesai')
                    @if(!$r->rating)
                        <button onclick="bukaRating('{{ $r->id_pesanan }}','{{ $r->nama_mitra }}')"
                                style="width:100%;background:var(--gold);color:#002d72;border:none;
                                       border-radius:34px;padding:12px;font-size:13px;font-weight:800;
                                       display:flex;align-items:center;justify-content:center;gap:8px;">
                            <i class="bi bi-star-fill"></i>BERI PENILAIAN
                        </button>
                    @else
                        <div style="text-align:center;color:var(--green);font-size:13px;font-weight:700;padding:5px;">
                            <i class="bi bi-patch-check-fill me-1"></i>Penilaian Sudah Dikirim
                        </div>
                    @endif
                @else
                    <a href="{{ route('pelanggan.riwayat.update', ['aksi'=>'selesai','id_order'=>$r->id_pesanan]) }}"
                       class="btn-z-primary d-block text-center text-decoration-none" style="padding:12px;"
                       onclick="return confirm('Konfirmasi pekerjaan sudah selesai?')">
                        KONFIRMASI SELESAI
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    @if($query_jastip->isEmpty() && $query_jasa->isEmpty())
        <div style="text-align:center;padding:55px 21px;color:var(--text-faint);">
            <i class="bi bi-receipt-cutoff" style="font-size:44px;display:block;margin-bottom:13px;opacity:.3;"></i>
            <div style="font-size:14px;font-weight:700;margin-bottom:8px;">Belum ada pesanan</div>
            <div style="font-size:12px;">Yuk mulai pesan layanan Zasha!</div>
        </div>
    @endif
</div>

{{-- ── MODAL RATING ─────────────────────────────── --}}
<div class="modal fade" id="modalRating" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="padding:21px;">
        <div class="modal-content" style="border-radius:21px;border:none;padding:21px;">
            <div style="text-align:center;margin-bottom:21px;">
                <div style="width:55px;height:55px;border-radius:13px;background:var(--gold-light);
                            display:flex;align-items:center;justify-content:center;margin:0 auto 13px;">
                    <i class="bi bi-star-fill" style="color:var(--gold);font-size:24px;"></i>
                </div>
                <div style="font-size:17px;font-weight:800;color:var(--text-main);margin-bottom:5px;">Kasih Nilai Jasa</div>
                <div id="namaMitraReview" style="font-size:12px;color:var(--text-muted);"></div>
            </div>
            <form action="{{ route('pelanggan.riwayat.ulasan') }}" method="POST">
                @csrf
                <input type="hidden" name="id_order" id="idOrderReview">
                <input type="hidden" name="rating_nilai" id="ratingNilai" value="5">
                <div style="display:flex;justify-content:center;gap:8px;margin-bottom:21px;">
                    @for($i=1;$i<=5;$i++)
                        <i class="bi bi-star-fill star-rating active" data-value="{{ $i }}"
                           onclick="setStar({{ $i }})"></i>
                    @endfor
                </div>
                <textarea name="ulasan_teks" rows="3" placeholder="Tulis komentar kamu…"
                          style="width:100%;border-radius:13px;border:1.5px solid var(--border);
                                 padding:13px;font-size:13px;font-family:inherit;resize:none;
                                 background:#f8fafc;margin-bottom:21px;"></textarea>
                <div style="display:flex;gap:8px;">
                    <button type="button" class="btn-z-ghost w-100" style="padding:13px;"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-z-primary w-100" style="padding:13px;">KIRIM</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
const ratingModal = new bootstrap.Modal(document.getElementById('modalRating'));
function bukaRating(id, nama) {
    document.getElementById('idOrderReview').value = id;
    document.getElementById('namaMitraReview').innerText = 'Bagaimana kepuasanmu terhadap ' + nama + '?';
    ratingModal.show();
}
function setStar(val) {
    document.getElementById('ratingNilai').value = val;
    document.querySelectorAll('.star-rating').forEach((s, i) => {
        s.classList.toggle('active', i < val);
    });
}
</script>
@endpush
@endsection
