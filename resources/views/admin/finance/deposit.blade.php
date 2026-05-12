@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')
@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag(); @endphp

<style>
    .deposit-stat-card {
        background: white;
        border: 1px solid var(--zasha-gray-200);
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        height: 100%;
    }
    .deposit-stat-icon {
        width: 44px; height: 44px;
        border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem;
        margin-bottom: 12px;
    }
    .deposit-stat-label {
        font-size: 0.7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px;
        color: var(--zasha-gray-400);
    }
    .deposit-stat-value {
        font-size: 1.1rem; font-weight: 800;
        color: var(--zasha-gray-900);
        margin-top: 4px;
        line-height: 1.2;
    }
    .deposit-stat-sub {
        font-size: 0.72rem;
        color: var(--zasha-gray-500);
        margin-top: 2px;
    }

    .quick-amount-btn {
        background: white;
        border: 1.5px solid var(--zasha-gray-200);
        border-radius: 10px;
        padding: 10px 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--zasha-gray-700);
    }
    .quick-amount-btn:hover {
        border-color: var(--zasha-blue);
        background: var(--zasha-blue-soft);
        color: var(--zasha-blue);
    }
    .quick-amount-btn.active {
        border-color: var(--zasha-blue);
        background: var(--zasha-blue);
        color: white;
    }

    .deposit-form-card {
        background: white;
        border: 1px solid var(--zasha-gray-200);
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .deposit-form-header {
        background: linear-gradient(135deg, var(--zasha-blue), var(--zasha-blue-dark));
        color: white;
        padding: 18px 24px;
    }
    .deposit-form-header h5 {
        margin: 0;
        font-size: 1rem; font-weight: 800;
    }
    .deposit-form-header small {
        opacity: 0.9;
        font-size: 0.75rem;
    }
    .deposit-form-body { padding: 24px; }

    .form-label-zasha {
        font-size: 0.78rem; font-weight: 700;
        color: var(--zasha-gray-700);
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 8px;
    }
    .form-input-zasha {
        border: 1.5px solid var(--zasha-gray-200);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 0.9rem;
        width: 100%;
        transition: all 0.15s;
    }
    .form-input-zasha:focus {
        border-color: var(--zasha-blue);
        box-shadow: 0 0 0 3px rgba(0,90,169,0.1);
        outline: none;
    }
    .input-with-prefix {
        position: relative;
    }
    .input-with-prefix .prefix {
        position: absolute;
        left: 14px; top: 50%; transform: translateY(-50%);
        color: var(--zasha-gray-500);
        font-weight: 700;
        font-size: 0.9rem;
    }
    .input-with-prefix .form-input-zasha {
        padding-left: 36px;
    }

    .type-radio-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .type-radio-card {
        border: 1.5px solid var(--zasha-gray-200);
        border-radius: 10px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.15s;
        text-align: center;
    }
    .type-radio-card:hover { border-color: var(--zasha-blue); }
    .type-radio-card input { display: none; }
    .type-radio-card.selected {
        border-color: var(--zasha-blue);
        background: var(--zasha-blue-soft);
    }
    .type-radio-card i {
        font-size: 1.6rem;
        color: var(--zasha-blue);
        display: block;
        margin-bottom: 6px;
    }
    .type-radio-card .label-name {
        font-size: 0.85rem; font-weight: 700;
        color: var(--zasha-gray-900);
    }
    .type-radio-card .label-desc {
        font-size: 0.7rem; color: var(--zasha-gray-500);
        margin-top: 2px;
    }

    .btn-deposit-submit {
        background: var(--zasha-blue);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 13px 24px;
        font-weight: 800;
        font-size: 0.9rem;
        width: 100%;
        transition: all 0.15s;
    }
    .btn-deposit-submit:hover {
        background: var(--zasha-blue-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,90,169,0.25);
    }
</style>

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-plus-circle-fill"></i> Deposit Manual</h4>
        <div class="zasha-page-subtitle">Tambah saldo secara manual ke akun Pelanggan atau Mitra.</div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 small">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── STATS ROW ────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="deposit-stat-card">
            <div class="deposit-stat-icon" style="background:#dbeafe; color:#1e40af;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="deposit-stat-label">Total Pelanggan</div>
            <div class="deposit-stat-value">{{ number_format($totalPelanggan ?? 0) }}</div>
            <div class="deposit-stat-sub">akun aktif</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="deposit-stat-card">
            <div class="deposit-stat-icon" style="background:#ede9fe; color:#7c3aed;">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="deposit-stat-label">Total Mitra</div>
            <div class="deposit-stat-value">{{ number_format($totalMitra ?? 0) }}</div>
            <div class="deposit-stat-sub">akun terdaftar</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="deposit-stat-card">
            <div class="deposit-stat-icon" style="background:#d1fae5; color:#065f46;">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="deposit-stat-label">Saldo Pelanggan</div>
            <div class="deposit-stat-value">Rp {{ number_format($totalSaldoPelanggan ?? 0, 0, ',', '.') }}</div>
            <div class="deposit-stat-sub">total saldo aktif</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="deposit-stat-card">
            <div class="deposit-stat-icon" style="background:#fef3c7; color:#92400e;">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="deposit-stat-label">Saldo Mitra</div>
            <div class="deposit-stat-value">Rp {{ number_format($totalSaldoMitra ?? 0, 0, ',', '.') }}</div>
            <div class="deposit-stat-sub">total saldo aktif</div>
        </div>
    </div>
</div>

{{-- ── MAIN GRID: FORM (8) + INFO (4) ──────────────── --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="deposit-form-card">
            <div class="deposit-form-header">
                <h5><i class="bi bi-cash-coin me-2"></i> Form Deposit Saldo</h5>
                <small>Isi data di bawah untuk menambah saldo akun. Aksi ini akan tercatat sebagai deposit manual oleh admin.</small>
            </div>
            <div class="deposit-form-body">
                <form action="{{ route('admin.finance.storeDeposit') }}" method="POST" id="form-deposit">
                    @csrf

                    {{-- Tipe User --}}
                    <div class="mb-4">
                        <label class="form-label-zasha">Pilih Tipe Akun</label>
                        <div class="type-radio-group">
                            <label class="type-radio-card {{ old('tipe_user') == 'Pelanggan' ? 'selected' : '' }}" data-type="Pelanggan">
                                <input type="radio" name="tipe_user" value="Pelanggan" {{ old('tipe_user') == 'Pelanggan' ? 'checked' : '' }} required>
                                <i class="bi bi-person-circle"></i>
                                <div class="label-name">Pelanggan</div>
                                <div class="label-desc">Tambah saldo ke akun pelanggan</div>
                            </label>
                            <label class="type-radio-card {{ old('tipe_user') == 'Mitra' ? 'selected' : '' }}" data-type="Mitra">
                                <input type="radio" name="tipe_user" value="Mitra" {{ old('tipe_user') == 'Mitra' ? 'checked' : '' }}>
                                <i class="bi bi-person-badge"></i>
                                <div class="label-name">Mitra</div>
                                <div class="label-desc">Tambah saldo ke akun mitra</div>
                            </label>
                        </div>
                        @error('tipe_user') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- User ID --}}
                    <div class="mb-4">
                        <label class="form-label-zasha">ID User</label>
                        <input type="number" name="user_id" class="form-input-zasha"
                               placeholder="Masukkan ID user (lihat di /admin/mitra atau /admin/pelanggan)"
                               value="{{ old('user_id') }}" min="1" required>
                        @error('user_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Quick Amount --}}
                    <div class="mb-3">
                        <label class="form-label-zasha">Nominal Cepat</label>
                        <div class="row g-2">
                            @foreach([10000, 50000, 100000, 500000, 1000000, 5000000] as $amount)
                                <div class="col-md-2 col-4">
                                    <div class="quick-amount-btn" data-amount="{{ $amount }}">
                                        Rp {{ number_format($amount/1000, 0, ',', '.') }}{{ $amount >= 1000000 ? 'jt' : 'rb' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Jumlah Nominal --}}
                    <div class="mb-4">
                        <label class="form-label-zasha">Atau Nominal Custom</label>
                        <div class="input-with-prefix">
                            <span class="prefix">Rp</span>
                            <input type="number" name="jumlah_nominal" id="input-nominal"
                                   class="form-input-zasha" placeholder="0"
                                   value="{{ old('jumlah_nominal') }}" min="1000" required>
                        </div>
                        @error('jumlah_nominal') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-4">
                        <label class="form-label-zasha">Keterangan (Opsional)</label>
                        <input type="text" name="keterangan" class="form-input-zasha"
                               placeholder="Misal: refund komplain, bonus loyalitas, kompensasi error sistem..."
                               value="{{ old('keterangan') }}">
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-deposit-submit">
                        <i class="bi bi-send-fill me-1"></i> Simpan Deposit
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── SIDE INFO ──────────────────────────────────── --}}
    <div class="col-lg-4">
        {{-- Tips Card --}}
        <div class="zasha-card mb-3">
            <div class="zasha-list-header">
                <h6><i class="bi bi-lightbulb-fill"></i> Tips Deposit Manual</h6>
            </div>
            <div class="p-3" style="font-size: 0.82rem; color: var(--zasha-gray-700); line-height: 1.6;">
                <div class="mb-3 d-flex gap-2 align-items-start">
                    <i class="bi bi-1-circle-fill text-primary"></i>
                    <span>Pastikan ID user benar. Bisa dicek di halaman <a href="{{ route('admin.mitra.index') }}">Manajemen Mitra</a>.</span>
                </div>
                <div class="mb-3 d-flex gap-2 align-items-start">
                    <i class="bi bi-2-circle-fill text-primary"></i>
                    <span>Tulis <strong>keterangan</strong> yang jelas untuk audit trail. Contoh: "Refund order #123 yang batal".</span>
                </div>
                <div class="mb-3 d-flex gap-2 align-items-start">
                    <i class="bi bi-3-circle-fill text-primary"></i>
                    <span>Aksi ini <strong>irreversible</strong> — saldo akan langsung bertambah. Cek dua kali sebelum submit.</span>
                </div>
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-4-circle-fill text-primary"></i>
                    <span>Untuk topup masuk dari bukti transfer pelanggan, gunakan <a href="{{ route('admin.finance.topup.index') }}">Konfirmasi Top-Up</a>.</span>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="zasha-card">
            <div class="zasha-list-header">
                <h6><i class="bi bi-lightning-charge-fill"></i> Aksi Cepat</h6>
            </div>
            <a href="{{ route('admin.finance.topup.index') }}" class="zasha-row">
                <div class="zasha-row-icon success"><i class="bi bi-cash-coin"></i></div>
                <div class="zasha-row-body">
                    <div class="zasha-row-title">Konfirmasi Top-Up</div>
                    <div class="zasha-row-meta">Validasi bukti transfer</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
            <a href="{{ route('admin.finance.withdrawal') }}" class="zasha-row">
                <div class="zasha-row-icon warning"><i class="bi bi-cash-stack"></i></div>
                <div class="zasha-row-body">
                    <div class="zasha-row-title">Withdrawal Mitra</div>
                    <div class="zasha-row-meta">Approve tarik saldo</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="zasha-row">
                <div class="zasha-row-icon purple"><i class="bi bi-graph-up"></i></div>
                <div class="zasha-row-body">
                    <div class="zasha-row-title">Dashboard Finance</div>
                    <div class="zasha-row-meta">Lihat statistik</div>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </a>
        </div>
    </div>
</div>

<script>
// Toggle visual selected pada type radio
document.querySelectorAll('.type-radio-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.type-radio-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
    });
});

// Quick amount buttons
document.querySelectorAll('.quick-amount-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.quick-amount-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('input-nominal').value = this.dataset.amount;
    });
});

// Clear quick amount when typing custom
document.getElementById('input-nominal').addEventListener('input', function() {
    document.querySelectorAll('.quick-amount-btn').forEach(b => b.classList.remove('active'));
});
</script>
@endsection
