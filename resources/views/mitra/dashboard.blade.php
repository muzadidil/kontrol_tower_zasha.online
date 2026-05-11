@extends('layouts.mitra')

@section('content')
<div class="page-pad stack-4">

    {{-- Header (fib-3 gap) --}}
    <div class="d-flex justify-content-between align-items-center" style="padding-top: var(--fib-2);">
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-34" style="background: linear-gradient(135deg,var(--mitra-blue),var(--mitra-blue-dark));display:flex;align-items:center;justify-content:center;box-shadow:0 var(--fib-1) var(--fib-3) rgba(0,90,169,0.25);">
                <i class="bi bi-person-fill text-white" style="font-size:var(--t-md);"></i>
            </div>
            <div>
                <div class="t-xs label-up" style="color:var(--ink-soft);margin-bottom:2px;">Halo,</div>
                <h6 class="fw-bold mb-0 allow-select t-lg" style="line-height:1;">{{ explode(' ', trim($mitra->nama_panggilan ?? $mitra->nama_asli ?? 'Mitra'))[0] }}</h6>
            </div>
        </div>
        <span class="{{ ($mitra->status_mitra ?? '') === 'aktif' ? 'badge-status-aktif' : 'badge-status-nonaktif' }}">
            {{ ucfirst($mitra->status_mitra ?? 'offline') }}
        </span>
    </div>

    {{-- Status Online/Offline Toggle --}}
    <div id="status-card" class="card-custom" style="padding: var(--fib-3); display: flex; align-items: center; justify-content: space-between;">
        <div style="flex: 1;">
            <div class="label-up" style="margin-bottom: 2px;">Status Kamu</div>
            <div id="status-label" style="font-size: var(--t-xs); font-weight: 700; margin-top: var(--fib-1);"
                 class="{{ $mitra->status_online === 'online' ? 'text-success' : 'text-secondary' }}">
                @if($mitra->status_online === 'online')
                    🟢 Online — Siap terima order
                @elseif($mitra->status_online === 'sibuk')
                    🟣 Sibuk — Sedang mengerjakan
                @else
                    ⚫ Offline — Tidak menerima order
                @endif
            </div>
        </div>
        <div class="form-check form-switch" style="padding-left: 0; margin-left: var(--fib-3);">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="toggleStatus"
                   {{ $mitra->status_online === 'online' ? 'checked' : '' }}
                   style="width: 48px; height: 24px; cursor: pointer;">
        </div>
    </div>

    {{-- Hero Saldo (Golden Ratio: padding fib-4, content centered) --}}
    <div class="hero-mitra">
        <div class="hero-content">
            <div class="d-flex justify-content-between align-items-start" style="margin-bottom: var(--fib-3);">
                <div>
                    <div class="label-up" style="color:rgba(255,255,255,0.7);">Saldo Mitra</div>
                    <h2 class="fw-bold mb-0 mt-1 allow-select" style="font-size: var(--t-2xl); letter-spacing: -0.02em;">
                        Rp {{ number_format($mitra->saldo ?? 0, 0, ',', '.') }}
                    </h2>
                </div>
                <div style="width: var(--fib-4); height: var(--fib-4); border-radius: 50%; background: rgba(179,218,253,0.4); display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-stars" style="color: var(--mitra-blue-soft); font-size: var(--t-xs);"></i>
                </div>
            </div>

            <div class="t-xxs allow-select" style="color: rgba(255,255,255,0.6); margin-bottom: var(--fib-3);">
                ID: {{ $mitra->id_mitra ?? '-' }}
            </div>

            <div class="d-flex" style="gap: var(--fib-2);">
                <a href="{{ route('mitra.saldo') }}" class="btn-mitra-ghost flex-grow-1 text-center" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--fib-1);">
                    <i class="bi bi-arrow-down-circle"></i>
                    <span>Tarik</span>
                </a>
                <a href="{{ route('mitra.topup.form') }}" class="btn-mitra-ghost flex-grow-1 text-center" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--fib-1);">
                    <i class="bi bi-plus-circle"></i>
                    <span>Topup</span>
                </a>
                <a href="{{ route('mitra.saldo') }}" class="btn-mitra-ghost flex-grow-1 text-center" style="display: inline-flex; align-items: center; justify-content: center; gap: var(--fib-1);">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats (golden 1:1.618 ratio - 2 cards, 3rd one hidden) --}}
    <div class="row g-3">
        <div class="col-6">
            <div class="card-custom" style="padding: var(--fib-3);">
                <div class="d-flex align-items-center" style="gap: var(--fib-2); margin-bottom: var(--fib-2);">
                    <div style="width: var(--fib-3); height: var(--fib-3); border-radius: var(--r-sm); background: var(--mitra-blue-light);"></div>
                    <span class="label-up t-xxs">Total Order</span>
                </div>
                <div class="fw-bold" style="font-size: var(--t-xl); color: var(--ink);">{{ $totalPesanan ?? 0 }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card-custom" style="padding: var(--fib-3);">
                <div class="d-flex align-items-center" style="gap: var(--fib-2); margin-bottom: var(--fib-2);">
                    <div style="width: var(--fib-3); height: var(--fib-3); border-radius: var(--r-sm); background: var(--mitra-blue-soft);"></div>
                    <span class="label-up t-xxs">Aktif</span>
                </div>
                <div class="fw-bold" style="font-size: var(--t-xl); color: var(--mitra-blue);">{{ $pesananAktif ?? 0 }}</div>
            </div>
        </div>
    </div>

    {{-- Active Order Progress (muncul saat order diterima) --}}
    <div id="active-order" style="display: none; margin-bottom: var(--fib-7);">
        <div class="card-custom" style="padding: var(--fib-3);">
            <div style="font-size: var(--t-xxs); font-weight: 800; color: var(--ink-soft); text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: var(--fib-3);">Order Aktif</div>

            <div style="margin-bottom: var(--fib-4);">
                <div style="font-size: var(--t-sm); font-weight: 700; color: var(--ink); margin-bottom: var(--fib-2);" id="active-pelanggan">-</div>
                <div style="font-size: var(--t-xs); color: var(--ink-soft);" id="active-layanan">-</div>
            </div>

            <div id="progress-steps">
                <div class="progress-step" data-step="menuju_lokasi">
                    <div class="step-dot"></div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <div class="step-title">Menuju Lokasi</div>
                        <button class="step-btn" onclick="updateProgress('menuju_lokasi')">
                            Saya Dalam Perjalanan
                        </button>
                    </div>
                </div>
                <div class="progress-step" data-step="di_lokasi">
                    <div class="step-dot"></div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <div class="step-title">Saya di Lokasi</div>
                        <button class="step-btn" onclick="updateProgress('di_lokasi')">
                            Saya Sudah Sampai
                        </button>
                    </div>
                </div>
                <div class="progress-step" data-step="dikerjakan">
                    <div class="step-dot"></div>
                    <div class="step-line"></div>
                    <div class="step-content">
                        <div class="step-title">Sedang Dikerjakan</div>
                        <button class="step-btn" onclick="updateProgress('dikerjakan')">
                            Mulai Kerjakan
                        </button>
                    </div>
                </div>
                <div class="progress-step" data-step="selesai_mitra">
                    <div class="step-dot"></div>
                    <div class="step-content">
                        <div class="step-title">Selesai</div>
                        <button class="step-btn" onclick="updateProgress('selesai_mitra')">
                            Pekerjaan Selesai
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Tiles (2x2 grid, fibonacci radius) --}}
    <div style="margin-bottom: var(--fib-7);">
        <div class="d-flex justify-content-between align-items-center" style="margin-bottom: var(--fib-3);">
            <h6 class="label-up mb-0">Fitur</h6>
            <span class="t-xs" style="color: var(--ink-soft);">{{ count([1,2,3,4]) }} menu</span>
        </div>
        <div class="row g-3">
            <div class="col-6">
                <a href="{{ route('mitra.pesanan') }}" class="menu-tile">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-soft); color: var(--mitra-blue);">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Pesanan</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Lihat semua</div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('mitra.saldo') }}" class="menu-tile">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-tint); color: var(--mitra-blue-mid);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Saldo</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Tarik & riwayat</div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('mitra.profil') }}" class="menu-tile">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-soft); color: var(--mitra-blue-dark);">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Profil</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Data akun</div>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <div class="menu-tile" style="opacity: 0.55; cursor: not-allowed;">
                    <div class="menu-tile-icon" style="background: var(--mitra-blue-tint); color: var(--mitra-blue-mid);">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <div class="fw-bold t-sm">Statistik</div>
                        <div class="t-xxs" style="color: var(--ink-soft);">Segera hadir</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Incoming Order Popup --}}
<div id="incoming-order" style="display: none; position: fixed; inset: 0; z-index: 9999;
     background: linear-gradient(180deg, #002d72 0%, #001d4d 100%);
     display: flex; flex-direction: column; align-items: center; justify-content: center;">

    <div style="text-align: center; margin-bottom: var(--fib-5);">
        <div style="width: 89px; height: 89px; border-radius: 50%; background: rgba(240, 165, 0, 0.2);
                    display: flex; align-items: center; justify-content: center; margin: 0 auto var(--fib-4);">
            <i class="bi bi-bell-fill" style="color: #f0a500; font-size: 34px;"></i>
        </div>
        <div style="font-size: var(--t-xxs); color: rgba(255, 255, 255, 0.6); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Order Masuk</div>
        <div id="order-nama" style="font-size: var(--t-lg); font-weight: 800; color: white; margin-top: var(--fib-2);">
            Nama Pelanggan
        </div>
        <div id="order-jenis" style="font-size: var(--t-sm); color: rgba(255, 255, 255, 0.7); margin-top: var(--fib-1);">
            Jenis Layanan
        </div>
        <div id="order-harga" style="font-size: var(--t-2xl); font-weight: 800; color: #f0a500; margin-top: var(--fib-4);">
            Rp 0
        </div>
    </div>

    <div style="width: 100%; max-width: 340px; padding: 0 var(--fib-4);">
        <button id="btn-tolak" onclick="showRejectOptions()"
                style="width: 100%; padding: var(--fib-4); border-radius: var(--r-lg); border: 2px solid rgba(239, 68, 68, 0.5);
                       background: rgba(239, 68, 68, 0.15); color: #fca5a5; font-size: var(--t-sm); font-weight: 800;
                       margin-bottom: var(--fib-3); cursor: pointer; transition: all 0.2s ease;">
            <i class="bi bi-x-circle me-2"></i>Tolak Order
        </button>

        <button id="btn-terima" onclick="acceptOrder()"
                style="width: 100%; padding: var(--fib-4); border-radius: var(--r-lg); border: none;
                       background: linear-gradient(135deg, #10b981, #059669); color: white;
                       font-size: var(--t-base); font-weight: 800; cursor: pointer; transition: all 0.2s ease;">
            <i class="bi bi-check-circle-fill me-2"></i>Terima Order
        </button>
    </div>
</div>

{{-- Reject Reason Panel --}}
<div id="reject-panel" style="display: none; position: fixed; bottom: 0; left: 50%;
     transform: translateX(-50%); width: 100%; max-width: var(--container-max); z-index: 10000;
     background: white; border-radius: var(--r-xl) var(--r-xl) 0 0; padding: var(--fib-4);">

    <div style="font-size: var(--t-sm); font-weight: 800; color: var(--ink); margin-bottom: var(--fib-3);">
        Alasan Menolak:
    </div>

    <button onclick="rejectOrder('Saya akan offline')" class="reject-opt">
        Saya akan offline
    </button>
    <button onclick="rejectOrder('Lokasi terlalu jauh dari posisi saya')" class="reject-opt">
        Lokasi terlalu jauh dari posisi saya
    </button>
    <button onclick="rejectOrder('Saya tidak bisa mengerjakan layanan ini')" class="reject-opt">
        Saya tidak bisa mengerjakan layanan ini
    </button>

    <div style="display: flex; gap: var(--fib-2); margin-top: var(--fib-2);">
        <input type="text" id="custom-reject" placeholder="Tulis alasan lain..."
               style="flex: 1; border: 1.5px solid var(--line); border-radius: var(--r-md); padding: var(--fib-3);
                      font-size: var(--t-xs); font-family: 'Plus Jakarta Sans';">
        <button onclick="rejectOrder(document.getElementById('custom-reject').value)"
                class="btn-reject-send">Kirim</button>
    </div>

    <button onclick="hideRejectPanel()" style="width: 100%; margin-top: var(--fib-3); padding: var(--fib-3);
            background: none; border: 1.5px solid var(--line); border-radius: var(--r-md);
            font-size: var(--t-xs); font-weight: 700; color: var(--ink-soft); cursor: pointer;
            transition: all 0.2s ease;">
        Batal
    </button>
</div>

@push('scripts')
<script>
let pollingInterval = null;
let currentOrderId = null;

// ─ Status Toggle ─
document.getElementById('toggleStatus').addEventListener('change', async function() {
    const isOnline = this.checked;
    const label = document.getElementById('status-label');
    const body = document.body;

    try {
        const response = await fetch('{{ route("mitra.toggle-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: isOnline ? 'online' : 'offline' })
        });

        const data = await response.json();

        if (data.success) {
            if (isOnline) {
                body.classList.remove('mitra-offline');
                label.className = 'text-success';
                label.textContent = '🟢 Online — Siap terima order';
                startPolling();
            } else {
                body.classList.add('mitra-offline');
                label.className = 'text-secondary';
                label.textContent = '⚫ Offline — Tidak menerima order';
                stopPolling();
            }
        } else {
            this.checked = !isOnline;
            alert('Gagal mengubah status: ' + data.message);
        }
    } catch (err) {
        this.checked = !isOnline;
        console.error('Toggle failed:', err);
        alert('Gagal mengubah status. Coba lagi.');
    }
});

// ─ Polling Pending Order ─
function startPolling() {
    console.log('Polling started');
    pollingInterval = setInterval(pollPendingOrder, 5000);
    pollPendingOrder(); // Check immediately
}

function stopPolling() {
    console.log('Polling stopped');
    if (pollingInterval) clearInterval(pollingInterval);
}

async function pollPendingOrder() {
    try {
        const resp = await fetch('{{ route("mitra.api.pending-order") }}');
        const data = await resp.json();

        if (data.order) {
            currentOrderId = data.order.id;
            showIncomingOrder(data.order);
        }
    } catch (e) {
        console.error('Polling error:', e);
    }
}

// ─ Show Incoming Order Popup ─
function showIncomingOrder(order) {
    currentOrderId = order.id;
    document.getElementById('order-nama').textContent = order.pelanggan.nama;
    document.getElementById('order-jenis').textContent = order.order_type;
    document.getElementById('order-harga').textContent = 'Rp ' + formatCurrency(order.harga_jual);
    document.getElementById('incoming-order').style.display = 'flex';
    stopPolling();
}

function hideIncomingOrder() {
    document.getElementById('incoming-order').style.display = 'none';
    startPolling();
}

// ─ Accept Order ─
async function acceptOrder() {
    if (!currentOrderId) return;

    try {
        const resp = await fetch('{{ route("mitra.order.accept") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tracking_id: currentOrderId })
        });

        const data = await resp.json();

        if (data.success) {
            hideIncomingOrder();
            showActiveOrder(currentOrderId);
            resetProgress();
        } else {
            alert(data.message);
        }
    } catch (e) {
        console.error('Accept failed:', e);
        alert('Gagal menerima order');
    }
}

// ─ Show Reject Reason Panel ─
function showRejectOptions() {
    document.getElementById('reject-panel').style.display = 'block';
}

function hideRejectPanel() {
    document.getElementById('reject-panel').style.display = 'none';
    hideIncomingOrder();
}

// ─ Reject Order ─
async function rejectOrder(reason) {
    if (!reason.trim()) {
        alert('Masukkan alasan penolakan');
        return;
    }

    if (!currentOrderId) return;

    try {
        const resp = await fetch('{{ route("mitra.order.reject") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tracking_id: currentOrderId,
                pesan: reason
            })
        });

        const data = await resp.json();

        if (data.success) {
            hideRejectPanel();
            document.getElementById('custom-reject').value = '';
            startPolling();
        } else {
            alert(data.message);
        }
    } catch (e) {
        console.error('Reject failed:', e);
        alert('Gagal menolak order');
    }
}

// ─ Show Active Order Section ─
function showActiveOrder(trackingId) {
    document.getElementById('active-order').style.display = 'block';
    // Scroll to active order
    document.getElementById('active-order').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ─ Reset Progress to First Step ─
function resetProgress() {
    document.querySelectorAll('.progress-step').forEach((step, idx) => {
        step.classList.remove('active', 'completed');
        if (idx === 0) step.classList.add('active');
    });
}

// ─ Update Progress ─
async function updateProgress(status) {
    if (!currentOrderId) return;

    try {
        const resp = await fetch('{{ route("mitra.order.progress") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tracking_id: currentOrderId,
                status: status
            })
        });

        const data = await resp.json();

        if (data.success) {
            markStepCompleted(status);
            markNextStepActive(status);
        } else {
            alert(data.message);
        }
    } catch (e) {
        console.error('Update progress failed:', e);
        alert('Gagal update status');
    }
}

// ─ Mark Step Completed ─
function markStepCompleted(step) {
    const el = document.querySelector(`.progress-step[data-step="${step}"]`);
    if (el) {
        el.classList.remove('active');
        el.classList.add('completed');
    }
}

// ─ Mark Next Step Active ─
function markNextStepActive(step) {
    const steps = ['menuju_lokasi', 'di_lokasi', 'dikerjakan', 'selesai_mitra'];
    const currentIdx = steps.indexOf(step);
    if (currentIdx >= 0 && currentIdx < steps.length - 1) {
        const nextStep = steps[currentIdx + 1];
        const nextEl = document.querySelector(`.progress-step[data-step="${nextStep}"]`);
        if (nextEl) nextEl.classList.add('active');
    }
}

// ─ Format Currency ─
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID').format(value);
}

// ─ Initialize Polling on Page Load ─
window.addEventListener('load', function() {
    const isOnline = document.getElementById('toggleStatus').checked;
    if (isOnline) {
        startPolling();
    }
    console.log('Dashboard initialized. Polling status:', isOnline ? 'active' : 'inactive');
});
</script>
@endpush

@endsection
