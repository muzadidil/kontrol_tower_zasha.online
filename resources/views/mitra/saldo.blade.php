@extends('layouts.mitra')

@section('content')
<div class="page-pad stack-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center" style="padding-top: var(--fib-2);">
        <div>
            <div class="label-up">Keuangan</div>
            <h5 class="fw-bold mb-0 t-lg" style="margin-top: var(--fib-1);">Saldo & Riwayat</h5>
        </div>
        <a href="{{ route('mitra.dashboard') }}" style="width: var(--fib-5); height: var(--fib-5); background: var(--surface); border-radius: var(--r-md); display: inline-flex; align-items: center; justify-content: center; color: var(--ink); text-decoration: none; box-shadow: 0 var(--fib-1) var(--fib-3) rgba(0,0,0,0.04);">
            <i class="bi bi-x-lg"></i>
        </a>
    </div>

    {{-- Hero Saldo (golden ratio) --}}
    <div class="hero-mitra">
        <div class="hero-content">
            <div class="d-flex justify-content-between align-items-center" style="margin-bottom: var(--fib-3);">
                <span class="label-up" style="color: rgba(255,255,255,0.7);">Saldo Tersedia</span>
                <i class="bi bi-shield-check" style="color: var(--mitra-blue-soft); font-size: var(--t-md);"></i>
            </div>
            <div class="fw-bold allow-select" style="font-size: var(--t-2xl); letter-spacing: -0.02em; line-height: 1;">
                Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}
            </div>
            <div class="t-xs" style="color: rgba(255,255,255,0.6); margin-top: var(--fib-2);">
                {{ $mitra->id_mitra ?? '-' }} · Min. tarik Rp 50.000
            </div>
        </div>
    </div>

    @foreach(['success','error'] as $t)
        @if(session($t))
            <div class="alert alert-{{ $t }} mb-0" style="border-radius: var(--r-md); padding: var(--fib-2) var(--fib-3); font-size: var(--t-xs);">
                {{ session($t) }}
            </div>
        @endif
    @endforeach

    {{-- Notif Topup Sukses --}}
    @if(session('notif_topup') == 'sukses')
        @php
            $s_nominal   = (int) session('data_nominal', 0);
            $s_kode_unik = (int) session('data_kode_unik', 0);
            $s_transfer  = (int) session('data_transfer', 0);
            $s_bank      = session('data_bank', 'DANA');
        @endphp
        <div class="card-custom" style="padding: var(--fib-4); border: 2px solid var(--mitra-blue); background: var(--mitra-blue-tint);">
            <div class="label-up" style="margin-bottom: var(--fib-2);">Transfer tepat sejumlah:</div>
            <div class="allow-select fw-bold" style="font-size: var(--t-xl); color: var(--mitra-blue); letter-spacing:-.02em; margin-bottom: var(--fib-3);">
                Rp {{ number_format($s_transfer, 0, ',', '.') }}
            </div>

            <div style="background: var(--mitra-blue-soft); border-radius: var(--r-md); padding: var(--fib-3); margin-bottom: var(--fib-3); border: 1px solid var(--mitra-blue-light);">
                <div class="label-up" style="margin-bottom: var(--fib-1);">Rincian Transfer</div>
                <div class="d-flex justify-content-between t-xs" style="margin-bottom: var(--fib-1);">
                    <span>Nominal:</span>
                    <strong>Rp {{ number_format($s_nominal, 0, ',', '.') }}</strong>
                </div>
                <div class="d-flex justify-content-between t-xs" style="border-top: 1px solid var(--mitra-blue-light); padding-top: var(--fib-1);">
                    <span>Kode Unik:</span>
                    <strong style="color: var(--mitra-blue);">+ {{ $s_kode_unik }}</strong>
                </div>
            </div>

            <div style="background: var(--bg-app); border-radius: var(--r-md); padding: var(--fib-3); margin-bottom: var(--fib-3);">
                <div class="label-up" style="margin-bottom: var(--fib-1);">Ke {{ $s_bank }}</div>
                @if($s_bank == 'BCA')
                    <div class="allow-select fw-bold" style="font-size: var(--t-lg); color: var(--mitra-blue);">1470807381</div>
                @else
                    <div class="allow-select fw-bold" style="font-size: var(--t-lg); color: var(--mitra-blue);">082232458226</div>
                @endif
                <div class="t-xs" style="color: var(--ink-soft);">a/n Muzadidil Fuad</div>
            </div>

            <div style="background: var(--mitra-blue-tint); border-radius: var(--r-md); padding: var(--fib-3); font-size: var(--t-xs); color: var(--mitra-blue-dark); line-height: 1.6; border: 1px solid var(--mitra-blue-light);">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Transfer <strong>tepat sesuai nominal</strong> termasuk kode unik di akhir agar otomatis terdeteksi.
            </div>
        </div>
    @endif

    {{-- Top Up --}}
    <div class="card-custom" style="padding: var(--fib-4);">
        <div class="d-flex align-items-center" style="gap: var(--fib-3); margin-bottom: var(--fib-3);">
            <div class="menu-tile-icon" style="background: var(--mitra-blue-soft); color: var(--mitra-blue);">
                <i class="bi bi-plus-circle-fill"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold t-sm">Isi Saldo</h6>
                <div class="t-xxs" style="color: var(--ink-soft);">Top up via transfer bank/e-wallet</div>
            </div>
        </div>

        <form action="{{ route('mitra.saldo.topup') }}" method="POST" class="stack-3">
            @csrf
            <div>
                <label class="t-xxs label-up" style="margin-bottom: var(--fib-1); display: block;">Media Transfer</label>
                <select name="bank_tujuan" class="form-select" style="border-radius: var(--r-md); padding: var(--fib-2) var(--fib-3); font-size: var(--t-sm);" required>
                    <option value="DANA">DANA — Muzadidil Fuad</option>
                    <option value="BCA">BCA — Muzadidil Fuad</option>
                </select>
            </div>

            <div>
                <label class="t-xxs label-up" style="margin-bottom: var(--fib-1); display: block;">Nominal</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: var(--mitra-blue-soft); border: none; font-weight: 700; color: var(--mitra-blue);">Rp</span>
                    <input type="number" name="nominal" class="form-control" placeholder="Min. 10.000"
                        min="10000" max="10000000" required
                        style="border-radius: 0 var(--r-md) var(--r-md) 0; padding: var(--fib-2) var(--fib-3); font-size: var(--t-sm);">
                </div>
                <div class="d-flex flex-wrap" style="gap: var(--fib-1); margin-top: var(--fib-2);">
                    @foreach([50000, 100000, 250000, 500000] as $nom)
                        <button type="button" onclick="setNominalTopup({{ $nom }})" class="btn"
                            style="background: var(--mitra-blue-soft); color: var(--mitra-blue); border: 1px solid var(--mitra-blue-light); border-radius: var(--r-pill); font-size: var(--t-xxs); font-weight: 700; padding: var(--fib-1) var(--fib-3);">
                            {{ number_format($nom, 0, ',', '.') }}
                        </button>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn-mitra-primary w-100" style="margin-top: var(--fib-2);">
                <i class="bi bi-arrow-up-circle me-2"></i>Request Top Up
            </button>
        </form>
    </div>

    {{-- Riwayat Top Up --}}
    @if(isset($riwayatTopup) && $riwayatTopup->isNotEmpty())
    <div>
        <h6 class="label-up" style="margin-bottom: var(--fib-3);">Riwayat Top Up</h6>
        <div class="stack-3">
            @foreach($riwayatTopup as $t)
                @php
                    $bg = '#e6f4ff'; $color = '#005aa9'; $statusLabel = $t->status_topup ?? 'Pending';
                    if(($t->status_topup ?? '') == 'Selesai') { $bg = '#005aa9'; $color = '#ffffff'; }
                    elseif(($t->status_topup ?? '') == 'Expired') { $bg = '#e1ecf7'; $color = '#6b7280'; }
                @endphp
                <div class="card-custom" style="padding: var(--fib-3);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold t-sm" style="color: var(--mitra-blue);">
                                +Rp {{ number_format($t->total_transfer ?? $t->jumlah_topup ?? 0, 0, ',', '.') }}
                            </div>
                            @if($t->kode_unik)
                                <div class="t-xxs" style="color: var(--ink-soft); margin-top: var(--fib-1);">
                                    Rp {{ number_format($t->jumlah_topup ?? 0, 0, ',', '.') }} + Kode {{ $t->kode_unik }} · {{ $t->bank_tujuan ?? 'Transfer' }}
                                </div>
                            @endif
                            <div class="t-xxs" style="color: var(--ink-soft); margin-top: var(--fib-1);">
                                {{ \Carbon\Carbon::parse($t->tanggal)->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <span style="background: {{ $bg }}; color: {{ $color }}; font-size: var(--t-xxs); padding: var(--fib-1) var(--fib-3); border-radius: var(--r-pill); font-weight: 700;">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tarik Dana --}}
    <div class="card-custom" style="padding: var(--fib-4);">
        <div class="d-flex align-items-center" style="gap: var(--fib-3); margin-bottom: var(--fib-3);">
            <div class="menu-tile-icon" style="background: var(--mitra-blue-tint); color: var(--mitra-blue-dark);">
                <i class="bi bi-arrow-down-circle-fill"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold t-sm">Tarik Dana</h6>
                <div class="t-xxs" style="color: var(--ink-soft);">Min. Rp 50.000 · Approval admin 1×24 jam</div>
            </div>
        </div>

        <form action="{{ route('mitra.withdrawal.request') }}" method="POST" class="stack-3">
            @csrf
            <div>
                <label class="t-xxs label-up" style="margin-bottom: var(--fib-1); display: block;">Bank / E-Wallet</label>
                <input type="text" name="bank_name" class="form-control" placeholder="BCA, Mandiri, GoPay..." required
                    style="border-radius: var(--r-md); padding: var(--fib-2) var(--fib-3); font-size: var(--t-sm);">
            </div>
            <div>
                <label class="t-xxs label-up" style="margin-bottom: var(--fib-1); display: block;">Nomor Rekening</label>
                <input type="text" name="account_number" class="form-control" placeholder="Nomor rekening tujuan" required
                    style="border-radius: var(--r-md); padding: var(--fib-2) var(--fib-3); font-size: var(--t-sm);">
            </div>
            <div>
                <label class="t-xxs label-up" style="margin-bottom: var(--fib-1); display: block;">Nama Pemilik (Sesuai KTP)</label>
                <input type="text" name="account_name" class="form-control" placeholder="Nama lengkap" required
                    style="border-radius: var(--r-md); padding: var(--fib-2) var(--fib-3); font-size: var(--t-sm);">
            </div>
            <div>
                <label class="t-xxs label-up" style="margin-bottom: var(--fib-1); display: block;">Nominal</label>
                <div class="input-group">
                    <span class="input-group-text" style="background: var(--mitra-blue-tint); border: none; font-weight: 700; color: var(--mitra-blue-dark);">Rp</span>
                    <input type="number" name="nominal" class="form-control" placeholder="50000" min="50000" required
                        style="border-radius: 0 var(--r-md) var(--r-md) 0; padding: var(--fib-2) var(--fib-3); font-size: var(--t-sm);">
                </div>
            </div>
            <button type="submit" class="btn-mitra-primary w-100" style="margin-top: var(--fib-2);">
                <i class="bi bi-arrow-down-circle me-2"></i>Ajukan Penarikan
            </button>
        </form>
    </div>

    {{-- Riwayat Penarikan --}}
    @if(isset($riwayat) && $riwayat->isNotEmpty())
    <div>
        <h6 class="label-up" style="margin-bottom: var(--fib-3);">Riwayat Penarikan</h6>
        <div class="stack-3">
            @foreach($riwayat as $r)
                @php
                    $bg = '#e6f4ff'; $color = '#005aa9';
                    if($r->status === 'approved') { $bg = '#005aa9'; $color = '#ffffff'; }
                    elseif($r->status === 'rejected') { $bg = '#e1ecf7'; $color = '#6b7280'; }
                @endphp
                <div class="card-custom" style="padding: var(--fib-3);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold t-sm">{{ $r->bank_name }}</div>
                            <div class="t-xxs" style="color: var(--ink-soft);">{{ $r->account_number }} · {{ $r->account_name }}</div>
                            <div class="t-xxs" style="color: var(--ink-soft); margin-top: var(--fib-1);">
                                {{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold t-sm" style="color: var(--mitra-blue-dark);">-Rp {{ number_format($r->nominal, 0, ',', '.') }}</div>
                            <span style="background: {{ $bg }}; color: {{ $color }}; font-size: var(--t-xxs); padding: var(--fib-1) var(--fib-3); border-radius: var(--r-pill); font-weight: 700; margin-top: var(--fib-1); display: inline-block;">
                                {{ ucfirst($r->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
function setNominalTopup(v) {
    document.querySelector('input[name=nominal]').value = v;
}
</script>
@endsection
