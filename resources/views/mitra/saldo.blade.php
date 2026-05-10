@extends('layouts.mitra')

@section('content')
<div class="container py-3">
    <h5 class="fw-bold mb-4 pt-2">Saldo & Keuangan</h5>

    {{-- Saldo Card --}}
    <div class="card card-custom p-4 mb-4 border-0" style="background:linear-gradient(135deg,#0a5c36,#1a7a4a);">
        <small class="text-white-50 fw-bold d-block mb-1" style="font-size:10px;">SALDO TERSEDIA</small>
        <h3 class="fw-bold text-white mb-0 allow-select">
            Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}
        </h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success small rounded-3 py-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger small rounded-3 py-2">{{ session('error') }}</div>
    @endif

    {{-- ── NOTIFIKASI TOPUP SUKSES ─────────────────── --}}
    @if(session('notif_topup') == 'sukses')
        @php
            $s_nominal    = (int) session('data_nominal', 0);
            $s_kode_unik  = (int) session('data_kode_unik', 0);
            $s_transfer   = (int) session('data_transfer', 0);
            $s_bank       = session('data_bank', 'DANA');
        @endphp
        <div class="card card-custom p-4 mb-4 border-0" style="border:2px solid #0a5c36 !important; background:#f0fdf4;">
            <div style="font-size:10px;font-weight:800;color:#6b7280;text-transform:uppercase;
                        letter-spacing:.5px;margin-bottom:8px;">Transfer tepat sejumlah:</div>
            <div class="allow-select" style="font-size:28px;font-weight:800;color:#0a5c36;
                        letter-spacing:-.5px;margin-bottom:13px;">
                Rp {{ number_format($s_transfer, 0, ',', '.') }}
            </div>
            <div style="background:#ecfdf5;border-radius:10px;padding:13px;margin-bottom:13px;border:1px solid #bbf7d0;">
                <div style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;
                            margin-bottom:5px;">Rincian Transfer:</div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                    <span style="font-size:12px;color:#1e293b;">Nominal Topup:</span>
                    <span style="font-size:12px;font-weight:700;color:#1e293b;">Rp {{ number_format($s_nominal, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid #d1fae5;padding-top:8px;">
                    <span style="font-size:12px;color:#1e293b;">Kode Unik:</span>
                    <span style="font-size:12px;font-weight:700;color:#0a5c36;">+ {{ $s_kode_unik }}</span>
                </div>
            </div>
            <div style="background:#f8fafc;border-radius:10px;padding:13px;margin-bottom:13px;">
                <div style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;
                            margin-bottom:5px;">Ke Rekening ({{ $s_bank }}):</div>
                @if($s_bank == 'BCA')
                    <div class="allow-select" style="font-size:21px;font-weight:800;color:#0a5c36;">1470807381</div>
                    <div style="font-size:12px;color:#6b7280;">a/n Muzadidil Fuad</div>
                @else
                    <div class="allow-select" style="font-size:21px;font-weight:800;color:#0a5c36;">082232458226</div>
                    <div style="font-size:12px;color:#6b7280;">a/n Muzadidil Fuad</div>
                @endif
            </div>
            <div style="background:#fff3cd;border-radius:10px;padding:13px;
                        font-size:11px;color:#92400e;line-height:1.6;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Transfer <strong>sesuai nominal</strong> termasuk 3 digit kode unik di akhir agar otomatis terdeteksi.
            </div>
        </div>
    @endif

    {{-- ── FORM ISI SALDO (TOP UP) ─────────────────── --}}
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold small text-muted text-uppercase mb-3">
            <i class="bi bi-plus-circle me-1"></i>Isi Saldo (Top Up)
        </h6>
        <form action="{{ route('mitra.saldo.topup') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Media Transfer</label>
                <select name="bank_tujuan" class="form-select rounded-3" required>
                    <option value="DANA">DANA — Muzadidil Fuad</option>
                    <option value="BCA">BCA — Muzadidil Fuad</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nominal</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="nominal" class="form-control rounded-end-3"
                           placeholder="Minimal 10.000" min="10000" max="10000000" required>
                </div>
                <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                    <button type="button" onclick="setNominalTopup(50000)"
                            class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:11px;">50.000</button>
                    <button type="button" onclick="setNominalTopup(100000)"
                            class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:11px;">100.000</button>
                    <button type="button" onclick="setNominalTopup(250000)"
                            class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:11px;">250.000</button>
                    <button type="button" onclick="setNominalTopup(500000)"
                            class="btn btn-sm btn-outline-secondary rounded-pill" style="font-size:11px;">500.000</button>
                </div>
            </div>
            <button type="submit" class="btn w-100 rounded-pill fw-bold text-white" style="background:#0a5c36;">
                <i class="bi bi-arrow-up-circle me-2"></i>Request Top Up
            </button>
        </form>
    </div>

    {{-- ── RIWAYAT TOP UP ──────────────────────────── --}}
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Riwayat Top Up</h6>
    @forelse($riwayatTopup as $t)
        @php
            $badgeClass = 'bg-warning text-dark';
            if(($t->status_topup ?? '') == 'Selesai') $badgeClass = 'bg-success';
            if(($t->status_topup ?? '') == 'Expired') $badgeClass = 'bg-danger';
        @endphp
        <div class="card card-custom p-3 mb-2">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold small text-success">
                        +Rp {{ number_format($t->total_transfer ?? $t->jumlah_topup ?? 0, 0, ',', '.') }}
                    </div>
                    <div class="text-muted" style="font-size:0.7rem;">
                        @if($t->kode_unik)
                            Nominal: Rp {{ number_format($t->jumlah_topup ?? 0, 0, ',', '.') }} + Kode: {{ $t->kode_unik }} &bull;
                        @endif
                        Via {{ $t->bank_tujuan ?? 'Transfer' }}
                    </div>
                    <div class="text-muted" style="font-size:0.65rem;">{{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y, H:i') }}</div>
                </div>
                <span class="badge rounded-pill {{ $badgeClass }}" style="font-size:0.6rem;">
                    {{ $t->status_topup }}
                </span>
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-muted small">Belum ada riwayat top up.</div>
    @endforelse

    <hr class="my-4">

    {{-- Form Tarik Dana --}}
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold small text-muted text-uppercase mb-3">Tarik Dana</h6>
        <form action="{{ route('mitra.withdrawal.request') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Bank / E-Wallet</label>
                <input type="text" name="bank_name" class="form-control rounded-3" placeholder="BCA, Mandiri, GoPay..." required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nomor Rekening</label>
                <input type="text" name="account_number" class="form-control rounded-3" placeholder="Nomor rekening tujuan" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Pemilik Rekening</label>
                <input type="text" name="account_name" class="form-control rounded-3" placeholder="Sesuai buku tabungan" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Nominal (Min. Rp 50.000)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="nominal" class="form-control rounded-end-3" placeholder="50000" min="50000" required>
                </div>
            </div>
            <button type="submit" class="btn w-100 rounded-pill fw-bold text-white" style="background:#0a5c36;">
                <i class="bi bi-arrow-down-circle me-2"></i>Ajukan Penarikan
            </button>
        </form>
    </div>

    {{-- Riwayat Penarikan --}}
    <h6 class="fw-bold small text-muted text-uppercase mb-3">Riwayat Penarikan</h6>
    @forelse($riwayat as $r)
        <div class="card card-custom p-3 mb-2">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="fw-bold small">{{ $r->bank_name }} — {{ $r->account_number }}</div>
                    <div class="text-muted" style="font-size:0.7rem;">{{ $r->account_name }}</div>
                    <div class="text-muted" style="font-size:0.65rem;">{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</div>
                </div>
                <div class="text-end">
                    <div class="fw-bold small text-danger">-Rp {{ number_format($r->nominal, 0, ',', '.') }}</div>
                    <span class="badge rounded-pill
                        @if($r->status === 'approved') bg-success
                        @elseif($r->status === 'rejected') bg-danger
                        @else bg-warning text-dark @endif"
                        style="font-size:0.6rem;">
                        {{ ucfirst($r->status) }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-4 text-muted small">Belum ada riwayat penarikan.</div>
    @endforelse
</div>

<script>
function setNominalTopup(v) {
    document.querySelector('input[name=nominal]').value = v;
}
</script>
@endsection
