@extends('layouts.mitra')

@section('content')
@php
    $mitra = Auth::guard('mitra')->user();
    $totalPending = $withdrawals->where('status', 'pending')->sum('nominal');
    $totalApproved = $withdrawals->where('status', 'approved')->sum('nominal');
@endphp

{{-- Hero --}}
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center mb-3">
        <a href="{{ route('mitra.dashboard') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">KEUANGAN</div>
            <h5 class="fw-bold m-0">Tarik Saldo</h5>
        </div>
    </div>

    {{-- Saldo aktif --}}
    <div class="text-center">
        <div style="font-size:.7rem;opacity:.8;">SALDO TERSEDIA</div>
        <div style="font-size:1.8rem;font-weight:800;letter-spacing:-.02em;">
            Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}
        </div>
        @if($totalPending > 0)
            <div style="font-size:.7rem;opacity:.7;">
                Rp {{ number_format($totalPending, 0, ',', '.') }} sedang diproses
            </div>
        @endif
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm small">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm small">
            <i class="bi bi-x-circle-fill me-1"></i>{{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-3 small">
            @foreach($errors->all() as $err)
                <div><i class="bi bi-x-circle-fill me-1"></i>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    {{-- Form Penarikan --}}
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <h6 class="fw-bold mb-3" style="color:#1e293b;">
                <i class="bi bi-bank me-1" style="color:#005aa9;"></i>Permintaan Penarikan Baru
            </h6>

            <form action="{{ route('mitra.withdrawal.request') }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">Nominal</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="nominal"
                               class="form-control rounded-end-3"
                               min="50000" max="10000000" step="1000"
                               placeholder="100000"
                               value="{{ old('nominal') }}" required>
                    </div>
                    <small class="text-muted" style="font-size:.7rem;">
                        Min Rp 50.000 · Max Rp 10.000.000
                    </small>
                </div>

                <div class="mb-2">
                    <label class="form-label small fw-bold mb-1">Bank</label>
                    <input type="text" name="bank_name"
                           class="form-control rounded-3"
                           placeholder="BCA / BRI / DANA / OVO"
                           value="{{ old('bank_name') }}"
                           maxlength="100" required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-7">
                        <label class="form-label small fw-bold mb-1">No. Rekening</label>
                        <input type="text" name="account_number"
                               class="form-control rounded-3"
                               placeholder="1234567890"
                               value="{{ old('account_number') }}"
                               maxlength="50" required>
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-bold mb-1">A.N.</label>
                        <input type="text" name="account_name"
                               class="form-control rounded-3"
                               placeholder="Nama"
                               value="{{ old('account_name') }}"
                               maxlength="100" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-bold">
                    <i class="bi bi-send-fill me-1"></i>Kirim Permintaan
                </button>
            </form>
        </div>
    </div>

    {{-- Riwayat --}}
    <h6 class="fw-bold mt-3 mb-2" style="color:#1e293b;">
        Riwayat Penarikan ({{ $withdrawals->count() }})
    </h6>

    @if($withdrawals->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <i class="bi bi-clipboard-x" style="font-size:2.5rem;color:#cbd5e1;"></i>
            <h6 class="mt-3 fw-bold">Belum Ada Riwayat</h6>
            <p class="text-muted small mb-0">Permintaan penarikan akan muncul di sini.</p>
        </div>
    @else
        @foreach($withdrawals as $w)
            @php
                $statusColor = match($w->status) {
                    'pending'  => ['#fef3c7', '#92400e', 'bi-clock-fill',         'Menunggu'],
                    'approved' => ['#d1fae5', '#065f46', 'bi-check-circle-fill',  'Disetujui'],
                    'rejected' => ['#fee2e2', '#991b1b', 'bi-x-circle-fill',      'Ditolak'],
                    default    => ['#f1f5f9', '#475569', 'bi-question-circle',    ucfirst($w->status)],
                };
            @endphp
            <div class="card border-0 shadow-sm rounded-4 mb-2">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold" style="color:#1e293b;">
                                Rp {{ number_format($w->nominal, 0, ',', '.') }}
                            </div>
                            <small class="text-muted" style="font-size:.7rem;">
                                {{ \Carbon\Carbon::parse($w->created_at)->translatedFormat('d M Y, H:i') }}
                            </small>
                        </div>
                        <span class="badge rounded-pill px-3 py-1"
                              style="background:{{ $statusColor[0] }};color:{{ $statusColor[1] }};font-size:.65rem;">
                            <i class="bi {{ $statusColor[2] }} me-1"></i>{{ $statusColor[3] }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#f8fafc;font-size:.8rem;">
                        <i class="bi bi-bank" style="color:#005aa9;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small" style="color:#1e293b;">{{ $w->bank_name }}</div>
                            <small class="text-muted" style="font-size:.7rem;">
                                {{ $w->account_number }} a.n. {{ $w->account_name }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="alert alert-info rounded-3 small mb-0"
         style="background:#eff6ff;border-color:#bfdbfe;color:#1e40af;">
        <i class="bi bi-info-circle-fill me-1"></i>
        Saldo dipotong saat permintaan dikirim. Jika ditolak admin, saldo otomatis dikembalikan.
    </div>
</div>
@endsection
