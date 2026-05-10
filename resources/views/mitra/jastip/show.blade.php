@extends('layouts.mitra')
@section('title', 'Detail ' . $order->order_code)

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <a href="{{ route('mitra.jastip.index') }}" class="btn btn-link text-warning ps-0 mb-3">
        <i class="fas fa-arrow-left me-1"></i> Daftar Jastip
    </a>

    @foreach(['success','error','warning','info'] as $type)
        @if(session($type)) <div class="alert alert-{{ $type }} alert-dismissible fade show">{{ session($type) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    @php $bm = ['menunggu_mitra'=>'warning','ditolak'=>'danger','menuju_pickup'=>'info','belanja'=>'info','menuju_pengantaran'=>'primary','diantar'=>'primary','menunggu_konfirmasi'=>'primary','selesai'=>'success','dispute'=>'danger']; @endphp

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="text-muted small">Order</div>
                    <div class="fw-bold fs-5">{{ $order->order_code }}</div>
                </div>
                <span class="badge bg-{{ $bm[$order->status->value] ?? 'secondary' }} fs-6 px-3 py-2">{{ ucwords(str_replace('_', ' ', $order->status->value)) }}</span>
            </div>
        </div>
    </div>

    {{-- Pelanggan Info --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2">Pelanggan</h6>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $order->pelanggan->nama_pelanggan ?? '-' }}</div>
                    @if($order->pelanggan->no_wa)
                    <a href="https://wa.me/{{ $order->pelanggan->no_wa }}" target="_blank" class="text-success small">
                        <i class="fab fa-whatsapp me-1"></i>{{ $order->pelanggan->no_wa }}
                    </a>
                    @endif
                </div>
                <a href="https://www.google.com/maps?q={{ $order->delivery_lat }},{{ $order->delivery_lng }}" target="_blank" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-map-marker-alt me-1"></i>Lihat Maps
                </a>
            </div>
            <div class="small text-muted mt-2">{{ $order->delivery_address }}</div>
        </div>
    </div>

    {{-- Action: Terima/Tolak --}}
    @if($order->status->value === 'menunggu_mitra')
    <div class="card border-0 shadow-sm border-start border-4 border-warning mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-2">Respons Diperlukan</h6>
            @if(! $order->cod_eligible)
                <div class="alert alert-warning small mb-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Saldo Anda saat ini tidak cukup untuk komisi (Rp {{ number_format($order->komisi_zasha, 0, ',', '.') }}). Topup dulu sebelum terima order.
                </div>
            @endif
            <div class="d-flex gap-2">
                <form action="{{ route('mitra.jastip.terima', $order->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success" {{ $order->cod_eligible ? '' : 'disabled' }}><i class="fas fa-check me-1"></i>Terima</button>
                </form>
                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                    <i class="fas fa-times me-1"></i>Tolak
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Stops & Items --}}
    @foreach($order->stops as $stop)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="fw-bold mb-1">{{ $loop->iteration }}. {{ $stop->nama_lokasi }}</h6>
                    <div class="small text-muted">{{ $stop->alamat_lokasi }}</div>
                    @if($stop->jarak_dari_prev_km > 0)
                        <div class="small text-muted">Jarak: {{ $stop->jarak_dari_prev_km }} km dari titik sebelumnya</div>
                    @endif
                </div>
                <a href="https://www.google.com/maps?q={{ $stop->lat }},{{ $stop->lng }}" target="_blank" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-directions"></i>
                </a>
            </div>

            {{-- Tombol Tiba --}}
            @if(in_array($order->status->value, ['menuju_pickup','belanja']) && ! $stop->tiba_at)
                <form action="{{ route('mitra.jastip.tiba-stop', $stop->id) }}" method="POST" class="mb-3">
                    @csrf
                    <button class="btn btn-warning btn-sm w-100"><i class="fas fa-map-marker-alt me-1"></i>Saya Tiba di Sini</button>
                </form>
            @elseif($stop->tiba_at)
                <div class="badge bg-success mb-3">✓ Tiba pukul {{ $stop->tiba_at->format('H:i') }}</div>
            @endif

            {{-- Items --}}
            @foreach($stop->items as $item)
            <div class="border rounded p-2 mb-2 {{ $item->is_checked ? 'bg-success bg-opacity-10' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-semibold small">{{ $item->nama_barang }}</div>
                        <div class="text-muted small">Estimasi: Rp {{ number_format($item->harga_perkiraan, 0, ',', '.') }}</div>
                        @if($item->catatan) <div class="text-muted small">📝 {{ $item->catatan }}</div> @endif
                    </div>
                    <div>
                        @if($item->is_checked)
                            <span class="badge bg-success">✓ Rp {{ number_format($item->harga_asli, 0, ',', '.') }}</span>
                        @elseif($order->status->value === 'belanja')
                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#checkModal{{ $item->id }}">
                                Centang
                            </button>
                        @else
                            <span class="badge bg-secondary">Belum</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Modal Checklist --}}
            @if(! $item->is_checked && $order->status->value === 'belanja')
            <div class="modal fade" id="checkModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0">
                        <div class="modal-header border-0"><h5 class="modal-title fw-bold">Checklist {{ $item->nama_barang }}</h5></div>
                        <form action="{{ route('mitra.jastip.checklist-item', $item->id) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <p class="small text-muted">Estimasi pelanggan: Rp {{ number_format($item->harga_perkiraan, 0, ',', '.') }}</p>
                                <label class="form-label small fw-semibold">Harga Asli</label>
                                <input type="number" name="harga_asli" class="form-control" required min="0" step="any">
                                <div class="form-text">Max deviasi 30%. Lebih dari itu, pelanggan perlu konfirmasi.</div>
                            </div>
                            <div class="modal-footer border-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                <button class="btn btn-success">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Tombol Aksi Akhir --}}
    @if($order->status->value === 'belanja' && $order->allItemsChecked())
    <form action="{{ route('mitra.jastip.mulai-antar', $order->id) }}" method="POST" class="mb-3">
        @csrf
        <button class="btn btn-primary w-100 fw-bold py-2"><i class="fas fa-truck me-1"></i>Selesai Belanja, Mulai Antar</button>
    </form>
    @endif

    @if($order->status->value === 'menuju_pengantaran')
    <form action="{{ route('mitra.jastip.diantar', $order->id) }}" method="POST" class="mb-3">
        @csrf
        <button class="btn btn-success w-100 fw-bold py-2"><i class="fas fa-check me-1"></i>Barang Sudah Diantar</button>
    </form>
    @endif

    {{-- Rincian --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3">Rincian Pembayaran</h6>
            <div class="d-flex justify-content-between small mb-1"><span>Total Belanja</span><span>Rp {{ number_format($order->actual_total_barang ?: $order->estimasi_total_barang, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small mb-1"><span>Ongkos Jasa</span><span>Rp {{ number_format($order->ongkos_jasa, 0, ',', '.') }}</span></div>
            <div class="d-flex justify-content-between small mb-1 text-danger"><span>Komisi Zasha (5%)</span><span>- Rp {{ number_format($order->komisi_zasha, 0, ',', '.') }}</span></div>
            <hr class="my-2">
            <div class="d-flex justify-content-between fw-bold text-success"><span>Pendapatan Bersih</span><span>Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}</span></div>
        </div>
    </div>
</div>

@if($order->status->value === 'menunggu_mitra')
<div class="modal fade" id="tolakModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold">Tolak Order</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="{{ route('mitra.jastip.tolak', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <textarea name="alasan" class="form-control" rows="3" placeholder="Alasan penolakan..." required minlength="10"></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger">Konfirmasi Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
