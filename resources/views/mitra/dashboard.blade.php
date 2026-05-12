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
    @php
        $statusOnline = $mitra->status_online;
        $statusLabelClass = match($statusOnline) {
            'online' => 'text-success',
            'sibuk'  => 'text-purple',
            default  => 'text-secondary',
        };
        $isToggleChecked = in_array($statusOnline, ['online', 'sibuk']);
        $isToggleDisabled = $statusOnline === 'sibuk';
    @endphp
    <div id="status-card" class="card-custom" style="padding: var(--fib-3); display: flex; align-items: center; justify-content: space-between;">
        <div style="flex: 1;">
            <div class="label-up" style="margin-bottom: 2px;">Status Kamu</div>
            <div id="status-label" style="font-size: var(--t-xs); font-weight: 700; margin-top: var(--fib-1); color: {{ $statusOnline === 'sibuk' ? '#9333ea' : '' }};"
                 class="{{ $statusLabelClass }}">
                @if($statusOnline === 'online')
                    🟢 Online — Siap terima order
                @elseif($statusOnline === 'sibuk')
                    🟣 Sibuk — Sedang mengerjakan order
                @else
                    ⚫ Offline — Tidak menerima order
                @endif
            </div>
        </div>
        <div class="form-check form-switch" style="padding-left: 0; margin-left: var(--fib-3);"
             title="{{ $isToggleDisabled ? 'Selesaikan order aktif terlebih dahulu' : '' }}">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="toggleStatus"
                   {{ $isToggleChecked ? 'checked' : '' }}
                   {{ $isToggleDisabled ? 'disabled' : '' }}
                   data-current-status="{{ $statusOnline }}"
                   style="width: 48px; height: 24px; cursor: {{ $isToggleDisabled ? 'not-allowed' : 'pointer' }}; {{ $statusOnline === 'sibuk' ? 'background-color: #9333ea; border-color: #9333ea;' : '' }}">
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

            {{-- Section: Pelanggan tandai BELUM SELESAI --}}
            <div id="belum-selesai-banner" style="display:none;margin-top:var(--fib-4);padding:var(--fib-3);background:#fff7ed;border:2px solid #fb923c;border-radius:var(--r-lg);">
                <div style="display:flex;align-items:center;gap:var(--fib-2);margin-bottom:var(--fib-2);">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#ea580c;font-size:21px;"></i>
                    <div style="font-weight:800;color:var(--ink);font-size:var(--t-sm);">
                        Pelanggan Tandai Belum Selesai
                    </div>
                </div>
                <div style="font-size:var(--t-xs);color:var(--ink-soft);line-height:1.5;margin-bottom:var(--fib-3);">
                    Pelanggan menunggu Anda menyelesaikan perbaikan. Klik tombol di bawah setelah selesai memperbaiki.
                </div>
                <button onclick="markPerbaikanSelesai()" style="width:100%;background:#10b981;color:white;border:none;border-radius:var(--r-md);padding:var(--fib-3);font-weight:800;font-size:var(--t-xs);cursor:pointer;">
                    <i class="bi bi-check-circle-fill me-1"></i>Saya Sudah Memperbaiki
                </button>
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
     flex-direction: column; align-items: center; justify-content: center;
     left: 50%; transform: translateX(-50%); width: 100%; max-width: 480px;">

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

    <div style="width: 100%; max-width: 340px; padding: 0 var(--fib-4); position: relative;">
        <!-- Slide-to-action track -->
        <div style="position: relative; height: 64px; border-radius: 34px;
                    background: rgba(255,255,255,0.1); border: 1.5px solid rgba(255,255,255,0.2);
                    display: flex; align-items: center; justify-content: space-between;
                    padding: 0 var(--fib-3); overflow: hidden;">

            <!-- Label: Tolak (left) -->
            <div style="font-size: var(--t-xxs); font-weight: 800; color: #fca5a5; z-index: 1;">
                <i class="bi bi-x-circle"></i> Tolak
            </div>

            <!-- Slider thumb (draggable) -->
            <div id="slider-thumb"
                 style="position: absolute; left: 50%; transform: translateX(-50%);
                        width: 64px; height: 48px; border-radius: 24px;
                        background: white; cursor: grab; z-index: 2;
                        display: flex; align-items: center; justify-content: center;
                        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
                        touch-action: none; user-select: none; transition: none;">
                <i class="bi bi-grip-vertical" style="color: #64748b; font-size: 21px;"></i>
            </div>

            <!-- Label: Terima (right) -->
            <div style="font-size: var(--t-xxs); font-weight: 800; color: #6ee7b7; z-index: 1;">
                Terima <i class="bi bi-check-circle"></i>
            </div>
        </div>

        <div style="text-align: center; margin-top: var(--fib-2); font-size: var(--t-xxs); color: rgba(255,255,255,0.5);">
            Geser ke kiri untuk tolak, ke kanan untuk terima
        </div>
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
        <button id="btn-kirim-alasan" class="btn-reject-send">Kirim</button>
    </div>

    <button onclick="hideRejectPanel()" style="width: 100%; margin-top: var(--fib-3); padding: var(--fib-3);
            background: none; border: 1.5px solid var(--line); border-radius: var(--r-md);
            font-size: var(--t-xs); font-weight: 700; color: var(--ink-soft); cursor: pointer;
            transition: all 0.2s ease;">
        Batal
    </button>
</div>

{{-- Toast Notification --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastNotif" class="toast align-items-center border-0 shadow-lg"
         role="alert" data-bs-autohide="true" data-bs-delay="4000">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toastMessage">
                Pesan notifikasi
            </div>
            <button type="button" class="btn-close me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ─ Toast Notification Helper ─
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('toastNotif');
    const toastMsg = document.getElementById('toastMessage');

    // Reset class
    toastEl.className = 'toast align-items-center border-0 shadow-lg';

    const typeMap = {
        'success': ['bg-success', 'text-white'],
        'error':   ['bg-danger',  'text-white'],
        'warning': ['bg-warning', 'text-dark'],
        'info':    ['bg-primary', 'text-white'],
    };
    (typeMap[type] || typeMap['info']).forEach(cls => toastEl.classList.add(cls));

    toastMsg.textContent = message;
    new bootstrap.Toast(toastEl).show();
}

// ─ Polling Functions ─
function startPolling() {
    if (pollingInterval) return;
    pollingInterval = setInterval(fetchPendingOrder, 10000); // Poll setiap 10 detik
    fetchPendingOrder(); // Fetch langsung saat mulai
}

function stopPolling() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}

async function fetchPendingOrder() {
    try {
        const response = await fetch('{{ route("mitra.api.pending-order") }}');
        const data = await response.json();

        if (data.order) {
            showIncomingOrder(data.order);
        }
    } catch (err) {
        console.error('Fetch pending order failed:', err);
    }
}

function showIncomingOrder(order) {
    currentOrderId = order.id;
    currentOrderData = order;

    document.getElementById('order-nama').textContent = order.pelanggan?.nama || 'Pelanggan';
    document.getElementById('order-jenis').textContent = order.order_type || 'Order';
    document.getElementById('order-harga').textContent = 'Rp ' + (order.harga_jual || 0).toLocaleString('id-ID');

    const incomingPopup = document.getElementById('incoming-order');
    if (incomingPopup) {
        incomingPopup.style.display = 'flex';
    }
}

// ─ Accept/Reject Functions ─
async function acceptOrder() {
    if (!currentOrderId) {
        showToast('Tidak ada order untuk diterima', 'warning');
        return;
    }

    // Konfirmasi sebelum accept untuk cegah accidental
    const confirmed = confirm('Yakin ingin menerima order ini?');
    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch('{{ route("mitra.order.accept") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tracking_id: currentOrderId })
        });

        const data = await response.json();

        if (data.success) {
            // Sembunyikan incoming popup
            const incomingPopup = document.getElementById('incoming-order');
            if (incomingPopup) {
                incomingPopup.style.display = 'none';
            }

            // Reset slider thumb ke tengah
            if (typeof window.resetSliderThumb === 'function') window.resetSliderThumb();

            // Update status UI ke sibuk tanpa reload
            updateStatusToBusy();

            // Stop polling karena sudah accept order
            stopPolling();

            // Tampilkan active order section
            showActiveOrder(currentOrderId);
            resetProgress();

            // Tampilkan tombol WA pelanggan
            showWhatsAppButton();

            showToast('Order berhasil diterima!', 'success');
        } else {
            showToast('Gagal menerima order: ' + (data.message || 'Silakan coba lagi'), 'error');
        }
    } catch (err) {
        console.error('Accept order failed:', err);
        showToast('Gagal menerima order, coba lagi', 'error');
    }
}

// Update UI status ke sibuk tanpa reload halaman
function updateStatusToBusy() {
    const label = document.getElementById('status-label');
    const toggle = document.getElementById('toggleStatus');
    const body = document.body;

    if (label) {
        label.textContent = '🟣 Sibuk — Sedang mengerjakan order';
        label.className = 'text-purple';
        label.style.color = '#9333ea';
    }
    if (toggle) {
        toggle.checked = true;
        toggle.disabled = true;
        toggle.dataset.currentStatus = 'sibuk';
        toggle.style.cursor = 'not-allowed';
        toggle.style.backgroundColor = '#9333ea';
        toggle.style.borderColor = '#9333ea';
    }
    if (body) {
        body.classList.remove('mitra-offline');
        body.classList.add('mitra-busy');
    }
}

function showRejectOptions() {
    const rejectPanel = document.getElementById('reject-panel');
    if (rejectPanel) {
        rejectPanel.style.display = 'block';
    }
}

function hideRejectPanel() {
    const rejectPanel = document.getElementById('reject-panel');
    if (rejectPanel) {
        rejectPanel.style.display = 'none';
    }
}

async function rejectOrder(alasan) {
    const trimmedAlasan = alasan ? alasan.trim() : '';

    if (!currentOrderId || !trimmedAlasan) {
        showToast('Silakan pilih atau masukkan alasan penolakan', 'warning');
        return;
    }

    try {
        const response = await fetch('{{ route("mitra.order.reject") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tracking_id: currentOrderId,
                pesan: trimmedAlasan
            })
        });

        const data = await response.json();

        if (data.success) {
            // Sembunyikan popups
            const incomingPopup = document.getElementById('incoming-order');
            const rejectPanel = document.getElementById('reject-panel');
            if (incomingPopup) incomingPopup.style.display = 'none';
            if (rejectPanel) rejectPanel.style.display = 'none';

            // Reset slider thumb ke tengah
            if (typeof window.resetSliderThumb === 'function') window.resetSliderThumb();

            // Reset form
            document.getElementById('custom-reject').value = '';

            // Reset state
            currentOrderId = null;
            currentOrderData = null;

            showToast('Order berhasil ditolak', 'info');

            // Lanjutkan polling
            setTimeout(fetchPendingOrder, 1000);
        } else {
            showToast('Gagal menolak order: ' + (data.message || 'Silakan coba lagi'), 'error');
        }
    } catch (err) {
        console.error('Reject order failed:', err);
        showToast('Terjadi kesalahan, silakan coba lagi', 'error');
    }
}

function showWhatsAppButton() {
    if (!currentOrderData || !currentOrderData.pelanggan) return;

    const activeOrder = document.getElementById('active-order');
    if (!activeOrder) return;

    const noWa = currentOrderData.pelanggan.no_wa || '';
    if (!noWa) return;

    let waButton = document.getElementById('btn-wa-pelanggan');
    if (!waButton) {
        const progressSteps = document.getElementById('progress-steps');
        if (progressSteps) {
            waButton = document.createElement('a');
            waButton.id = 'btn-wa-pelanggan';
            waButton.href = `https://wa.me/${noWa}?text=Halo, saya sedang menuju lokasimu. Sampai dalam beberapa menit.`;
            waButton.target = '_blank';
            waButton.style.cssText = 'display: inline-flex; align-items: center; gap: var(--fib-2); background: #25d366; color: white; padding: var(--fib-3); border-radius: var(--r-md); text-decoration: none; font-weight: 700; margin-top: var(--fib-3);';
            waButton.innerHTML = '<i class="bi bi-whatsapp"></i>Hubungi via WhatsApp';
            progressSteps.appendChild(waButton);
        }
    }
}

// Status Toggle - only on dashboard
const toggleEl = document.getElementById('toggleStatus');
if (toggleEl) {
    toggleEl.addEventListener('change', async function(e) {
        // Block jika sedang sibuk (mengerjakan order)
        if (this.dataset.currentStatus === 'sibuk' || this.disabled) {
            this.checked = true; // Force tetap checked
            e.stopPropagation();
            showToast('Selesaikan order aktif terlebih dahulu sebelum mengubah status', 'warning');
            return;
        }

        // Prevent toggle if user just finished swiping (event propagation issue)
        if (isSwiping) {
            this.checked = !this.checked; // Revert checkbox
            e.stopPropagation();
            return;
        }

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
                    body.classList.remove('mitra-busy');
                    label.className = 'text-success';
                    label.style.color = '';
                    label.textContent = '🟢 Online — Siap terima order';
                    this.dataset.currentStatus = 'online';
                    this.style.backgroundColor = '';
                    this.style.borderColor = '';
                    startPolling();
                } else {
                    body.classList.add('mitra-offline');
                    body.classList.remove('mitra-busy');
                    label.className = 'text-secondary';
                    label.style.color = '';
                    label.textContent = '⚫ Offline — Tidak menerima order';
                    this.dataset.currentStatus = 'offline';
                    this.style.backgroundColor = '';
                    this.style.borderColor = '';
                    stopPolling();
                }
            } else {
                this.checked = !isOnline;
                showToast('Gagal mengubah status: ' + (data.message || 'Silakan coba lagi'), 'error');
            }
        } catch (err) {
            this.checked = !isOnline;
            console.error('Toggle failed:', err);
            showToast('Gagal mengubah status, coba lagi', 'error');
        }
    });
}

// Init polling hanya jika mitra benar-benar online (tidak sibuk/offline)
document.addEventListener('DOMContentLoaded', () => {
    @if($mitra->status_online === 'online')
        startPolling();
    @endif

    // Restore active order section dari server-side data
    @isset($activeTracking)
        @if($activeTracking)
            currentOrderId = {{ $activeTracking->id }};
            currentOrderData = {
                id: {{ $activeTracking->id }},
                order_type: '{{ $activeTracking->order_type }}',
                status: '{{ $activeTracking->status }}',
                pelanggan: {
                    nama: '{{ addslashes($activeTracking->pelanggan_nama ?? "Pelanggan") }}',
                    no_wa: '{{ $activeTracking->pelanggan_wa ?? "" }}'
                }
            };

            // Tampilkan active order section
            const activeEl = document.getElementById('active-order');
            if (activeEl) activeEl.style.display = 'block';

            // Restore progress steps berdasar status
            restoreProgressFromStatus('{{ $activeTracking->status }}');

            // Tampilkan banner "Belum Selesai" jika perlu
            @if($activeTracking->status === 'belum_selesai')
                const banner = document.getElementById('belum-selesai-banner');
                if (banner) banner.style.display = 'block';
            @endif

            // Start polling kalau order sudah selesai_mitra atau belum_selesai
            @if(in_array($activeTracking->status, ['selesai_mitra', 'belum_selesai']))
                startActiveOrderPolling();
            @endif
        @endif
    @endisset
});

// Restore progress steps tampilan dari status active order
function restoreProgressFromStatus(status) {
    const orderedSteps = ['menuju_lokasi', 'di_lokasi', 'dikerjakan', 'selesai_mitra'];
    const statusMap = {
        'accepted': -1,           // belum mulai progress
        'menuju_lokasi': 0,
        'di_lokasi': 1,
        'dikerjakan': 2,
        'selesai_mitra': 3,
        'belum_selesai': 3,       // tetap di selesai_mitra
    };
    const currentIdx = statusMap[status] ?? -1;

    document.querySelectorAll('.progress-step').forEach((el, idx) => {
        el.classList.remove('active', 'completed');
        if (idx < currentIdx) {
            el.classList.add('completed');
        } else if (idx === currentIdx) {
            el.classList.add(status === 'selesai_mitra' ? 'completed' : 'active');
        }
    });
}

// Show Active Order Section - only on dashboard
function showActiveOrder(trackingId) {
    const activeOrder = document.getElementById('active-order');
    if (activeOrder) {
        activeOrder.style.display = 'block';
        activeOrder.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Reset Progress - only on dashboard
function resetProgress() {
    const steps = document.querySelectorAll('.progress-step');
    steps.forEach((step, idx) => {
        step.classList.remove('active', 'completed');
        if (idx === 0) step.classList.add('active');
    });
}

// Update Progress - only on dashboard
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

            // Setelah klik "Pekerjaan Selesai" → mulai polling apakah pelanggan
            // konfirmasi atau klik "Belum Selesai"
            if (status === 'selesai_mitra') {
                startActiveOrderPolling();
            }
        } else {
            showToast(data.message || 'Gagal update status', 'error');
        }
    } catch (e) {
        console.error('Update progress failed:', e);
        showToast('Gagal update status, coba lagi', 'error');
    }
}

// Mitra menandai perbaikan selesai (setelah pelanggan klik "Belum Selesai")
async function markPerbaikanSelesai() {
    if (!currentOrderId) {
        showToast('Tidak ada order aktif', 'warning');
        return;
    }

    const confirmed = confirm('Yakin perbaikan sudah selesai? Pelanggan akan diminta konfirmasi ulang.');
    if (!confirmed) return;

    try {
        const resp = await fetch('{{ route("mitra.order.progress") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                tracking_id: currentOrderId,
                status: 'selesai_mitra'
            })
        });

        const data = await resp.json();

        if (data.success) {
            // Sembunyikan banner belum selesai
            const banner = document.getElementById('belum-selesai-banner');
            if (banner) banner.style.display = 'none';
            showToast('Berhasil! Pelanggan akan diminta konfirmasi ulang.', 'success');
        } else {
            showToast(data.message || 'Gagal update status', 'error');
        }
    } catch (e) {
        console.error('markPerbaikanSelesai failed:', e);
        showToast('Gagal kirim status, coba lagi', 'error');
    }
}

// Polling status active order untuk deteksi pelanggan klik "Belum Selesai" / konfirmasi
let activeOrderPollingInterval = null;

function startActiveOrderPolling() {
    if (activeOrderPollingInterval) return;
    activeOrderPollingInterval = setInterval(checkActiveOrderStatus, 5000);
    checkActiveOrderStatus();
}

function stopActiveOrderPolling() {
    if (activeOrderPollingInterval) {
        clearInterval(activeOrderPollingInterval);
        activeOrderPollingInterval = null;
    }
}

async function checkActiveOrderStatus() {
    if (!currentOrderId) {
        stopActiveOrderPolling();
        return;
    }

    try {
        const resp = await fetch(`/order-tracking/${currentOrderId}/status`);
        if (!resp.ok) return;
        const data = await resp.json();

        if (!data.success) return;

        const banner = document.getElementById('belum-selesai-banner');

        if (data.status === 'belum_selesai') {
            // Pelanggan tandai belum selesai → tampilkan banner perbaikan
            if (banner) banner.style.display = 'block';
        } else if (data.status === 'selesai') {
            // Pelanggan konfirmasi selesai → reload untuk update tampilan
            stopActiveOrderPolling();
            if (banner) banner.style.display = 'none';
            showToast('Pelanggan mengonfirmasi selesai! Saldo bertambah.', 'success');
            setTimeout(() => location.reload(), 1500);
        } else if (data.status === 'selesai_mitra') {
            // Sembunyikan banner kalau sudah balik ke selesai_mitra
            if (banner) banner.style.display = 'none';
        }
    } catch (e) {
        console.error('checkActiveOrderStatus failed:', e);
    }
}

// Mark Step Completed
function markStepCompleted(step) {
    const el = document.querySelector(`.progress-step[data-step="${step}"]`);
    if (el) {
        el.classList.remove('active');
        el.classList.add('completed');
    }
}

// Mark Next Step Active
function markNextStepActive(step) {
    const steps = ['menuju_lokasi', 'di_lokasi', 'dikerjakan', 'selesai_mitra'];
    const currentIdx = steps.indexOf(step);
    if (currentIdx >= 0 && currentIdx < steps.length - 1) {
        const nextStep = steps[currentIdx + 1];
        const nextEl = document.querySelector(`.progress-step[data-step="${nextStep}"]`);
        if (nextEl) nextEl.classList.add('active');
    }
}

// ─ Slide-to-Action Logic (3-zone snap: 0% / 50% / 100%) ─
let isSwiping = false; // Flag to prevent accidental toggle trigger after swipe

(function initSlideToAction() {
    const thumb = document.getElementById('slider-thumb');
    if (!thumb) return;

    const track = thumb.parentElement;
    const trackWidth = track.offsetWidth;
    const thumbWidth = thumb.offsetWidth;
    // Jarak penuh ke kiri/kanan = setengah lebar track dikurangi setengah lebar thumb
    const maxSlide = (trackWidth - thumbWidth) / 2 - 8;
    // Snap zone: 50% dari maxSlide → posisi pertengahan ke kiri/kanan
    // Kalau geser >= snap point → snap ke ujung
    const SNAP_POINT = maxSlide * 0.5;

    let startX = 0, currentX = 0;
    let isDragging = false, dragStartTime = 0;

    function getClientX(e) {
        return e.touches ? e.touches[0].clientX : e.clientX;
    }

    function safePreventDefault(e) {
        if (e && e.cancelable) e.preventDefault();
    }

    function setThumbPosition(x) {
        thumb.style.left = `calc(50% + ${x}px)`;
    }

    function setThumbVisual(x) {
        const icon = thumb.querySelector('i');
        if (x > SNAP_POINT * 0.5) {
            thumb.style.background = '#10b981';
            if (icon) icon.style.color = 'white';
        } else if (x < -SNAP_POINT * 0.5) {
            thumb.style.background = '#ef4444';
            if (icon) icon.style.color = 'white';
        } else {
            thumb.style.background = 'white';
            if (icon) icon.style.color = '#64748b';
        }
    }

    function resetThumb(animated = true) {
        thumb.style.transition = animated ? 'all 0.25s ease' : 'none';
        setThumbPosition(0);
        thumb.style.background = 'white';
        const icon = thumb.querySelector('i');
        if (icon) icon.style.color = '#64748b';
    }

    function cancelDrag() {
        if (!isDragging) return;
        isDragging = false;
        thumb.style.cursor = 'grab';
        currentX = 0;
        resetThumb(true);
        setTimeout(() => { isSwiping = false; }, 100);
    }

    thumb.addEventListener('mousedown', startDrag);
    thumb.addEventListener('touchstart', startDrag, { passive: false });

    function startDrag(e) {
        isDragging = true;
        isSwiping = true;
        startX = getClientX(e);
        currentX = 0;
        dragStartTime = Date.now();
        thumb.style.cursor = 'grabbing';
        thumb.style.transition = 'none';
        safePreventDefault(e);
        e.stopPropagation();
    }

    document.addEventListener('mousemove', onDrag);
    document.addEventListener('touchmove', onDrag, { passive: false });

    function onDrag(e) {
        if (!isDragging) return;
        safePreventDefault(e);
        e.stopPropagation();

        const diff = getClientX(e) - startX;
        currentX = Math.max(-maxSlide, Math.min(maxSlide, diff));
        setThumbPosition(currentX);
        setThumbVisual(currentX);
    }

    document.addEventListener('mouseup', endDrag);
    document.addEventListener('touchend', endDrag);
    document.addEventListener('touchcancel', cancelDrag);
    document.addEventListener('mouseleave', cancelDrag);
    window.addEventListener('blur', cancelDrag);

    function endDrag(e) {
        if (!isDragging) return;
        isDragging = false;
        safePreventDefault(e);
        e.stopPropagation();

        thumb.style.cursor = 'grab';
        thumb.style.transition = 'all 0.25s ease';

        const finalX = currentX;
        const dragDuration = Date.now() - dragStartTime;

        console.log(`Swipe end: finalX=${finalX}px, maxSlide=${maxSlide}px, snapPoint=±${SNAP_POINT}px, duration=${dragDuration}ms`);

        // ── 3-Zone Snap Logic ──
        // Zone KIRI (currentX <= -SNAP_POINT) → snap ke -maxSlide (100% kiri) → TOLAK
        // Zone TENGAH (-SNAP_POINT < currentX < SNAP_POINT) → balik ke 0 → TIDAK ADA AKSI
        // Zone KANAN (currentX >= SNAP_POINT) → snap ke +maxSlide (100% kanan) → TERIMA

        if (finalX >= SNAP_POINT) {
            // Snap ke kanan penuh
            setThumbPosition(maxSlide);
            thumb.style.background = '#10b981';
            const icon = thumb.querySelector('i');
            if (icon) icon.style.color = 'white';
            console.log('SNAP RIGHT → Accept Order');
            // Delay sebentar agar user lihat snap animation
            setTimeout(() => acceptOrder(), 150);
        } else if (finalX <= -SNAP_POINT) {
            // Snap ke kiri penuh
            setThumbPosition(-maxSlide);
            thumb.style.background = '#ef4444';
            const icon = thumb.querySelector('i');
            if (icon) icon.style.color = 'white';
            console.log('SNAP LEFT → Show Reject Options');
            setTimeout(() => showRejectOptions(), 150);
        } else {
            // Snap kembali ke tengah
            console.log('SNAP CENTER (no action)');
            currentX = 0;
            resetThumb(true);
        }

        // Clear swipe flag setelah animation selesai
        setTimeout(() => { isSwiping = false; }, 300);
    }

    // Reset thumb saat popup ditutup
    window.resetSliderThumb = function() {
        currentX = 0;
        resetThumb(false);
    };
})();

// Event listener untuk tombol Kirim alasan custom
document.addEventListener('DOMContentLoaded', function() {
    const btnKirim = document.getElementById('btn-kirim-alasan');
    if (btnKirim) {
        btnKirim.addEventListener('click', function() {
            const alasan = document.getElementById('custom-reject').value.trim();
            if (!alasan) {
                showToast('Tulis alasan penolakan terlebih dahulu', 'warning');
                return;
            }
            rejectOrder(alasan);
        });
    }
});
</script>
@endpush

@endsection
