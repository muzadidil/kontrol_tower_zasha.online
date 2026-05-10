@extends('layouts.pelanggan')

@section('content')

{{-- ── HERO ─────────────────────────────────────── --}}
<div style="background:linear-gradient(145deg,#001d4d 0%,#002d72 55%,#0047b3 100%);
            padding:21px; position:relative; overflow:hidden;">
    <div style="position:absolute;top:-34px;right:-34px;width:144px;height:144px;
                border-radius:50%;background:rgba(240,165,0,.1);"></div>
    <div style="position:absolute;bottom:-21px;left:21px;width:89px;height:89px;
                border-radius:50%;background:rgba(255,255,255,.05);"></div>

    <div style="display:flex;align-items:center;gap:13px;margin-bottom:21px;">
        <a href="{{ route('pelanggan.dashboard') }}"
           style="width:34px;height:34px;border-radius:8px;background:rgba(255,255,255,.1);
                  display:flex;align-items:center;justify-content:center;text-decoration:none;">
            <i class="bi bi-arrow-left" style="color:white;font-size:16px;"></i>
        </a>
        <span style="font-size:16px;font-weight:800;color:white;">Dompet ZASHA</span>
    </div>

    <div style="font-size:11px;color:rgba(255,255,255,.55);font-weight:600;
                text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Saldo Saya</div>
    <div class="allow-select" style="font-size:32px;font-weight:800;color:#fff;
                letter-spacing:-.5px;margin-bottom:5px;">
        Rp {{ number_format($pelanggan->saldo ?? 0, 0, ',', '.') }}
    </div>
    <div style="font-size:11px;color:rgba(255,255,255,.45);">Tersedia untuk digunakan</div>
</div>

{{-- ── TOP UP CARD ─────────────────────────────── --}}
<div style="padding:13px 21px 0;">
    <div class="z-card" style="padding:21px;margin-bottom:13px;">

        @if(session('success'))
            <div style="background:#d1fae5;border-radius:13px;padding:13px;margin-bottom:21px;
                        display:flex;gap:10px;align-items:flex-start;">
                <i class="bi bi-check-circle-fill" style="color:#10b981;font-size:18px;flex-shrink:0;"></i>
                <div style="font-size:12px;color:#065f46;font-weight:600;">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('notif_topup') == 'sukses')
            @php
                $s_nominal    = (int) session('data_nominal', 0);
                $s_kode_unik  = (int) session('data_kode_unik', 0);
                $s_transfer   = (int) session('data_transfer', 0);
                $s_bank       = session('data_bank', 'DANA');
            @endphp
            <div style="border:1.5px solid #0047b3;border-radius:21px;padding:21px;margin-bottom:21px;
                        background:#eff6ff;">
                <div style="font-size:10px;font-weight:800;color:var(--text-faint);text-transform:uppercase;
                            letter-spacing:.5px;margin-bottom:8px;">Transfer tepat sejumlah:</div>
                <div class="allow-select" style="font-size:28px;font-weight:800;color:#002d72;
                            letter-spacing:-.5px;margin-bottom:13px;">
                    Rp {{ number_format($s_transfer, 0, ',', '.') }}
                </div>
                <div style="background:#f0f9ff;border-radius:13px;padding:13px;margin-bottom:13px;border:1px solid #bfdbfe;">
                    <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;
                                margin-bottom:5px;">Rincian Transfer:</div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                        <span style="font-size:12px;color:var(--text-main);">Nominal Topup:</span>
                        <span style="font-size:12px;font-weight:700;color:var(--text-main);">Rp {{ number_format($s_nominal, 0, ',', '.') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;border-top:1px solid #e0e7ff;padding-top:8px;">
                        <span style="font-size:12px;color:var(--text-main);">Kode Unik:</span>
                        <span style="font-size:12px;font-weight:700;color:#0047b3;">+ {{ $s_kode_unik }}</span>
                    </div>
                </div>
                <div style="background:#f8fafc;border-radius:13px;padding:13px;margin-bottom:13px;">
                    <div style="font-size:10px;font-weight:700;color:var(--text-faint);text-transform:uppercase;
                                margin-bottom:5px;">Ke Rekening ({{ $s_bank }}):</div>
                    @if($s_bank == 'BCA')
                        <div class="allow-select" style="font-size:21px;font-weight:800;color:#002d72;">1470807381</div>
                        <div style="font-size:12px;color:var(--text-muted);">a/n Muzadidil Fuad</div>
                    @else
                        <div class="allow-select" style="font-size:21px;font-weight:800;color:#002d72;">082232458226</div>
                        <div style="font-size:12px;color:var(--text-muted);">a/n Muzadidil Fuad</div>
                    @endif
                </div>
                <div style="background:#fff3cd;border-radius:13px;padding:13px;
                            font-size:11px;color:#92400e;line-height:1.6;">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    Transfer <strong>sesuai nominal</strong> termasuk 3 digit kode unik di akhir agar otomatis terdeteksi.
                </div>
            </div>
        @endif

        <button onclick="toggleForm()" id="btnTopup"
                style="width:100%;background:var(--gold);color:#002d72;border:none;
                       border-radius:34px;padding:13px;font-size:14px;font-weight:800;
                       display:flex;align-items:center;justify-content:center;gap:8px;transition:.2s;">
            <i class="bi bi-plus-circle-fill"></i>ISI SALDO (TOP UP)
        </button>

        <div id="areaForm" style="display:none;margin-top:21px;border-top:1px solid var(--border);padding-top:21px;">
            <form action="{{ route('pelanggan.dompet.topup') }}" method="POST">
                @csrf
                <div style="margin-bottom:13px;">
                    <div style="font-size:10px;font-weight:700;color:var(--text-faint);
                                text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Media Transfer</div>
                    <select name="bank_tujuan" class="form-select" style="border-radius:13px;border-color:var(--border);
                            font-size:13px;font-weight:600;" required>
                        <option value="DANA">DANA — Muzadidil Fuad</option>
                        <option value="BCA">BCA — Muzadidil Fuad</option>
                    </select>
                </div>
                <div style="margin-bottom:13px;">
                    <div style="font-size:10px;font-weight:700;color:var(--text-faint);
                                text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Nominal</div>
                    <input type="number" name="nominal" class="form-control" placeholder="Minimal Rp 10.000"
                           style="border-radius:13px;border-color:var(--border);font-size:15px;font-weight:700;
                                  padding:12px 16px;" required>
                    <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                        @foreach([50000,100000,250000,500000] as $n)
                            <button type="button" onclick="setNominal({{ $n }})"
                                    style="background:#eff6ff;color:#002d72;border:1px solid #bfdbfe;
                                           border-radius:34px;padding:5px 13px;font-size:11px;font-weight:700;">
                                {{ number_format($n,0,',','.') }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div style="margin-bottom:21px;">
                    <div style="font-size:10px;font-weight:700;color:var(--text-faint);
                                text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Nama Pengirim</div>
                    <input type="text" name="nomor_rekening" class="form-control" placeholder="Sesuai nama rekening"
                           style="border-radius:13px;border-color:var(--border);font-size:13px;padding:12px 16px;" required>
                </div>
                <button type="submit" class="btn-z-primary w-100" style="padding:13px;font-size:13px;">
                    KONFIRMASI SEKARANG
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ── RIWAYAT ──────────────────────────────────── --}}
<div style="padding:0 21px 34px;">
    <div class="divider-label">Riwayat Isi Saldo</div>

    @forelse($riwayat as $r)
        @php
            $badgeStyle = 'background:#fef9c3;color:#b45309;';
            if(($r->status ?? '') == 'sukses') $badgeStyle = 'background:#d1fae5;color:#065f46;';
            if(($r->status ?? '') == 'batal')  $badgeStyle = 'background:#fee2e2;color:#991b1b;';
        @endphp
        <div class="z-card" style="padding:16px 21px;margin-bottom:8px;display:flex;
                                   align-items:center;gap:13px;">
            <div style="width:42px;height:42px;border-radius:13px;background:#eff6ff;
                        display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="bi bi-wallet2" style="color:#002d72;font-size:18px;"></i>
            </div>
            <div style="flex:1;">
                <div style="font-size:14px;font-weight:800;color:var(--text-main);">
                    Rp {{ number_format($r->total_transfer ?? $r->nominal ?? 0, 0, ',', '.') }}
                </div>
                <div style="font-size:11px;color:var(--text-muted);">
                    @if($r->kode_unik)
                        Nominal: Rp {{ number_format($r->nominal ?? 0, 0, ',', '.') }} + Kode: {{ $r->kode_unik }} &bull;
                    @endif
                    Via {{ $r->bank_tujuan ?? '-' }} &bull;
                    {{ isset($r->waktu_request) ? date('d M, H:i', strtotime($r->waktu_request)) : '-' }}
                </div>
            </div>
            <span style="{{ $badgeStyle }}border-radius:34px;font-size:9px;font-weight:800;
                          padding:4px 10px;text-transform:uppercase;">
                {{ ucfirst($r->status ?? '-') }}
            </span>
        </div>
    @empty
        <div style="text-align:center;padding:34px 0;color:var(--text-faint);">
            <i class="bi bi-clock-history" style="font-size:34px;display:block;margin-bottom:13px;opacity:.4;"></i>
            <div style="font-size:13px;font-weight:600;">Belum ada riwayat transaksi</div>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
function toggleForm() {
    var f = document.getElementById('areaForm');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
    if (f.style.display === 'block') f.scrollIntoView({behavior:'smooth', block:'nearest'});
}
function setNominal(v) {
    document.querySelector('input[name=nominal]').value = v;
}
</script>
@endpush
@endsection
