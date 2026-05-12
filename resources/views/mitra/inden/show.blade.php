@extends('layouts.mitra')

@section('content')
@php
    $statusLabel = $order->status instanceof \App\Enums\IndenOrderStatus ? $order->status->label() : $order->status;
    $isMenungguMitra = $order->status === \App\Enums\IndenOrderStatus::MenungguMitra;
    $isDpDibayar = $order->status === \App\Enums\IndenOrderStatus::DpDibayar;
    $isDikerjakan = $order->status === \App\Enums\IndenOrderStatus::Dikerjakan;
    $nama = $order->pelanggan->nama_panggilan ?? $order->pelanggan->nama_pelanggan ?? 'Pelanggan';
    $tanggal = \Carbon\Carbon::parse($order->tanggal_pelaksanaan);
@endphp

{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.inden.index') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">ORDER INDEN</div>
            <h5 class="fw-bold m-0">#{{ $order->order_code ?? $order->id }}</h5>
        </div>
    </div>
    <div class="text-center">
        <div class="badge rounded-pill px-3 py-2" style="background:rgba(255,255,255,.2);color:#fff;">
            {{ $statusLabel }}
        </div>
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 small">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Tanggal Pelaksanaan (highlight) --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3" style="background:linear-gradient(135deg,#eff6ff,#fff);">
        <div class="card-body text-center p-3">
            <small class="text-muted text-uppercase fw-bold" style="font-size:.65rem;">Tanggal Pelaksanaan</small>
            <div class="fw-bold" style="color:#005aa9;font-size:1.5rem;">
                {{ $tanggal->translatedFormat('d M Y') }}
            </div>
            <small class="text-muted">
                {{ $tanggal->translatedFormat('l') }} · {{ $tanggal->diffForHumans() }}
            </small>
        </div>
    </div>

    {{-- Pelanggan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:50%;background:#eff6ff;color:#005aa9;
                            display:flex;align-items:center;justify-content:center;font-size:1.5rem;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="color:#1e293b;">{{ $nama }}</div>
                    @if($order->pelanggan && $order->pelanggan->no_wa)
                        <small class="text-muted">
                            <i class="bi bi-whatsapp text-success"></i> {{ $order->pelanggan->no_wa }}
                        </small>
                    @endif
                </div>
                @if($order->pelanggan && $order->pelanggan->no_wa)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->pelanggan->no_wa) }}"
                       target="_blank"
                       class="btn btn-sm btn-success rounded-pill px-3"
                       style="background:#25d366;border:none;">
                        <i class="bi bi-whatsapp"></i> Chat
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Order --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b;font-size:.85rem;">Detail Order</h6>

            <div class="row g-2">
                <div class="col-6">
                    <small class="text-muted" style="font-size:.65rem;">Durasi</small>
                    <div class="fw-bold small">{{ $order->durasi }} {{ $order->tipe_durasi }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted" style="font-size:.65rem;">Tarif</small>
                    <div class="fw-bold small">Rp {{ number_format($order->tarif, 0, ',', '.') }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted" style="font-size:.65rem;">Total Biaya</small>
                    <div class="fw-bold small">Rp {{ number_format($order->total_biaya, 0, ',', '.') }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted" style="font-size:.65rem;">Pendapatan Anda</small>
                    <div class="fw-bold small" style="color:#16a34a;">
                        Rp {{ number_format($order->pendapatan_mitra, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if($order->keterangan_kerja)
                <hr class="my-2">
                <small class="text-muted" style="font-size:.65rem;">Keterangan Pekerjaan</small>
                <div class="small">{{ $order->keterangan_kerja }}</div>
            @endif
        </div>
    </div>

    {{-- Lokasi --}}
    @if($order->alamat_pelanggan)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-2" style="color:#1e293b;font-size:.85rem;">
                    <i class="bi bi-geo-alt-fill" style="color:#ef4444;"></i> Lokasi
                </h6>
                <div class="small text-muted">{{ $order->alamat_pelanggan }}</div>
                @if($order->pelanggan_lat && $order->pelanggan_lng)
                    <a href="https://www.google.com/maps?q={{ $order->pelanggan_lat }},{{ $order->pelanggan_lng }}"
                       target="_blank"
                       class="btn btn-sm btn-outline-primary rounded-pill mt-2 px-3">
                        <i class="bi bi-map"></i> Buka Maps
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Pembayaran Status --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b;font-size:.85rem;">Status Pembayaran</h6>
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <small class="text-muted" style="font-size:.65rem;">DP</small>
                    <div class="fw-bold small">Rp {{ number_format($order->dp_amount, 0, ',', '.') }}</div>
                </div>
                @if($order->dp_paid_at)
                    <span class="badge bg-success rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i>Dibayar
                    </span>
                @else
                    <span class="badge bg-warning text-dark rounded-pill">
                        <i class="bi bi-clock-fill me-1"></i>Menunggu
                    </span>
                @endif
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2">
                <div>
                    <small class="text-muted" style="font-size:.65rem;">Pelunasan</small>
                    <div class="fw-bold small">Rp {{ number_format($order->pelunasan_amount, 0, ',', '.') }}</div>
                </div>
                @if($order->pelunasan_paid_at)
                    <span class="badge bg-success rounded-pill">
                        <i class="bi bi-check-circle-fill me-1"></i>Dibayar
                    </span>
                @else
                    <span class="badge bg-secondary rounded-pill">
                        <i class="bi bi-hourglass-split me-1"></i>Belum
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Alasan Penolakan (kalau ditolak) --}}
    @if($order->alasan_penolakan)
        <div class="alert alert-danger rounded-4 mb-3">
            <strong>Alasan Penolakan:</strong> {{ $order->alasan_penolakan }}
        </div>
    @endif

    {{-- Action Buttons --}}
    @if($isMenungguMitra)
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3" style="color:#1e293b;">Konfirmasi Order</h6>
                <form action="{{ route('mitra.inden.approve', $order->id) }}" method="POST" class="d-grid mb-2">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill py-2 fw-bold"
                            onclick="return confirm('Setujui order inden untuk tanggal {{ $tanggal->format('d M Y') }}?')">
                        <i class="bi bi-check-circle-fill me-1"></i>Setujui Order
                    </button>
                </form>

                <button type="button"
                        class="btn btn-outline-danger w-100 rounded-pill py-2 fw-bold"
                        onclick="document.getElementById('form-tolak').style.display='block';this.style.display='none';">
                    <i class="bi bi-x-circle me-1"></i>Tolak Order
                </button>

                <form action="{{ route('mitra.inden.tolak', $order->id) }}" method="POST"
                      id="form-tolak" style="display:none;" class="mt-3">
                    @csrf
                    <label class="form-label small fw-bold">Alasan Penolakan</label>
                    <textarea name="alasan" class="form-control rounded-3 mb-2"
                              rows="3" maxlength="500"
                              placeholder="contoh: jadwal bentrok dengan order lain"
                              required></textarea>
                    <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold">
                        <i class="bi bi-send-fill me-1"></i>Kirim Penolakan
                    </button>
                </form>
            </div>
        </div>
    @elseif($isDpDibayar)
        <form action="{{ route('mitra.inden.mulaiKerja', $order->id) }}" method="POST" class="d-grid mb-3">
            @csrf
            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold"
                    onclick="return confirm('Mulai kerjakan order ini?')">
                <i class="bi bi-play-fill me-1"></i>Mulai Kerja
            </button>
        </form>
    @elseif($isDikerjakan)
        <form action="{{ route('mitra.inden.selesaiKerja', $order->id) }}" method="POST" class="d-grid mb-3">
            @csrf
            <button type="submit" class="btn btn-success rounded-pill py-2 fw-bold"
                    onclick="return confirm('Tandai order ini SELESAI? Pelanggan akan diminta bayar pelunasan.')">
                <i class="bi bi-check-square me-1"></i>Selesai Kerja
            </button>
        </form>
    @endif
</div>
@endsection
