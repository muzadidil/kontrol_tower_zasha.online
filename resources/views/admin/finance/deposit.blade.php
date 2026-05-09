@extends('layouts.admin')

@section('content')
<style>
    .form-label-sm { font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
    .form-control, .form-select { border-color: #e5e7eb; border-radius: 8px; font-size: 0.875rem; padding: 10px 14px; }
    .form-control:focus, .form-select:focus { border-color: #005aa9; box-shadow: 0 0 0 3px rgba(0,90,169,0.08); }
    .tipe-card { border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; cursor: pointer; transition: .15s; text-align: center; }
    .tipe-card:hover { border-color: #005aa9; background: #eff6ff; }
    .tipe-card input[type=radio] { display: none; }
    .tipe-card.selected { border-color: #005aa9; background: #eff6ff; }
    .tipe-card .tipe-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; }
    .input-rp { position: relative; }
    .input-rp::before { content: 'Rp'; position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: #6b7280; font-size: 0.875rem; z-index: 5; pointer-events: none; }
    .input-rp input { padding-left: 38px !important; }
    .quick-nominal { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
    .quick-nominal button { font-size: 0.75rem; padding: 4px 12px; border-radius: 20px; border: 1px solid #e5e7eb; background: white; color: #374151; transition: .15s; }
    .quick-nominal button:hover { border-color: #005aa9; color: #005aa9; background: #eff6ff; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-plus-circle me-2" style="color:#005aa9;"></i>Deposit Manual
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Tambah saldo secara langsung ke akun mitra atau pelanggan.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 rounded-4 p-4" style="box-shadow:0 2px 12px rgba(0,0,0,0.08);">

            <form action="{{ route('admin.finance.storeDeposit') }}" method="POST" id="depositForm">
                @csrf

                {{-- Tipe User --}}
                <div class="mb-4">
                    <label class="form-label-sm">Tipe Akun</label>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="tipe-card d-block" id="card-pelanggan">
                                <input type="radio" name="tipe_user" value="Pelanggan" checked onchange="selectTipe(this)">
                                <div class="tipe-icon" style="background:#eff6ff;">
                                    <i class="fas fa-user" style="color:#005aa9;"></i>
                                </div>
                                <div class="fw-semibold" style="font-size:0.85rem; color:#1e293b;">Pelanggan</div>
                                <div style="font-size:0.72rem; color:#94a3b8;">Akun pengguna jasa</div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="tipe-card d-block" id="card-mitra">
                                <input type="radio" name="tipe_user" value="Mitra" onchange="selectTipe(this)">
                                <div class="tipe-icon" style="background:#f0fdf4;">
                                    <i class="fas fa-hard-hat" style="color:#16a34a;"></i>
                                </div>
                                <div class="fw-semibold" style="font-size:0.85rem; color:#1e293b;">Mitra</div>
                                <div style="font-size:0.72rem; color:#94a3b8;">Akun penyedia jasa</div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- User ID --}}
                <div class="mb-3">
                    <label class="form-label-sm">ID Pengguna</label>
                    <input type="number" name="user_id" class="form-control" placeholder="Masukkan ID akun tujuan..." required>
                    <div class="mt-1" style="font-size:0.72rem; color:#94a3b8;">
                        <i class="fas fa-info-circle me-1"></i>Cek ID di halaman Manajemen Mitra atau data pelanggan.
                    </div>
                </div>

                {{-- Nominal --}}
                <div class="mb-3">
                    <label class="form-label-sm">Nominal Deposit</label>
                    <div class="input-rp">
                        <input type="number" name="jumlah_nominal" id="nominalInput" class="form-control"
                               placeholder="0" min="1000" required>
                    </div>
                    <div class="quick-nominal">
                        <button type="button" onclick="setNominal(50000)">50.000</button>
                        <button type="button" onclick="setNominal(100000)">100.000</button>
                        <button type="button" onclick="setNominal(250000)">250.000</button>
                        <button type="button" onclick="setNominal(500000)">500.000</button>
                        <button type="button" onclick="setNominal(1000000)">1.000.000</button>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="mb-4">
                    <label class="form-label-sm">Keterangan (Opsional)</label>
                    <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Koreksi saldo, Kompensasi, dll...">
                </div>

                <button type="submit" class="btn w-100 fw-bold rounded-pill py-2"
                        style="background:#005aa9; color:white; font-size:0.875rem;">
                    <i class="fas fa-paper-plane me-2"></i>Proses Deposit
                </button>
            </form>

        </div>
    </div>
</div>

<script>
function selectTipe(radio) {
    document.getElementById('card-pelanggan').classList.remove('selected');
    document.getElementById('card-mitra').classList.remove('selected');
    if (radio.value === 'Pelanggan') document.getElementById('card-pelanggan').classList.add('selected');
    else document.getElementById('card-mitra').classList.add('selected');
}
function setNominal(val) {
    document.getElementById('nominalInput').value = val;
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('card-pelanggan').classList.add('selected');
});
</script>
@endsection
