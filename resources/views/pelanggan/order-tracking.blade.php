@extends('layouts.pelanggan')

@section('content')
<div style="padding: var(--s3);">

    {{-- Header --}}
    <div style="margin-bottom: var(--s4);">
        <a href="javascript:history.back()" style="display:inline-flex;align-items:center;gap:var(--s2);color:var(--blue-deep);text-decoration:none;font-weight:700;font-size:13px;">
            <i class="bi bi-chevron-left"></i> Kembali
        </a>
        <h5 style="font-weight:800;color:var(--text-main);margin-top:var(--s2);margin-bottom:0;">
            Tracking Order #{{ $tracking->id }}
        </h5>
    </div>

    {{-- Mitra Info Card --}}
    <div class="z-card" style="padding:var(--s3);margin-bottom:var(--s4);display:flex;gap:var(--s3);align-items:center;">
        <div style="width:55px;height:55px;border-radius:var(--r2);background:var(--blue-pale);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-person-fill" style="color:var(--blue-deep);font-size:21px;"></i>
        </div>
        <div style="flex:1;">
            <div style="font-size:11px;color:var(--text-faint);text-transform:uppercase;font-weight:700;margin-bottom:2px;">Mitra Pengerjaan</div>
            <div style="font-weight:800;color:var(--text-main);margin-bottom:4px;">
                {{ $tracking->mitra->nama_panggilan ?? $tracking->mitra->nama_asli }}
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tracking->mitra->no_wa) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;color:var(--blue-deep);text-decoration:none;font-weight:700;">
                <i class="bi bi-whatsapp"></i> Hubungi
            </a>
        </div>
    </div>

    {{-- Progress Tracker (Read-only) --}}
    <div class="z-card" style="padding:var(--s3);margin-bottom:var(--s4);">
        <div style="font-size:11px;font-weight:800;color:var(--text-faint);text-transform:uppercase;letter-spacing:0.6px;margin-bottom:var(--s3);">Progress</div>

        <div id="progress-steps">
            <div class="progress-step-read" data-step="menuju_lokasi">
                <div class="step-dot-read"></div>
                <div class="step-line-read"></div>
                <div class="step-content-read">
                    <div style="font-size:13px;font-weight:700;color:var(--text-muted);">Menuju Lokasi</div>
                </div>
            </div>
            <div class="progress-step-read" data-step="di_lokasi">
                <div class="step-dot-read"></div>
                <div class="step-line-read"></div>
                <div class="step-content-read">
                    <div style="font-size:13px;font-weight:700;color:var(--text-muted);">Saya di Lokasi</div>
                </div>
            </div>
            <div class="progress-step-read" data-step="dikerjakan">
                <div class="step-dot-read"></div>
                <div class="step-line-read"></div>
                <div class="step-content-read">
                    <div style="font-size:13px;font-weight:700;color:var(--text-muted);">Sedang Dikerjakan</div>
                </div>
            </div>
            <div class="progress-step-read" data-step="selesai_mitra">
                <div class="step-dot-read"></div>
                <div class="step-content-read">
                    <div style="font-size:13px;font-weight:700;color:var(--text-muted);">Selesai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Section (muncul saat selesai_mitra) --}}
    <div id="section-confirm" style="display:none;margin-bottom:var(--s4);">
        <div class="z-card" style="padding:var(--s3);text-align:center;background:#f0fdf4;border:2px solid var(--green);">
            <i class="bi bi-check-circle-fill" style="color:var(--green);font-size:34px;"></i>
            <div style="font-size:16px;font-weight:800;color:var(--text-main);margin-top:var(--s2);">
                Mitra Selesai Mengerjakan
            </div>
            <div style="font-size:11px;color:var(--text-muted);margin-top:var(--s1);">
                Silakan cek terlebih dahulu hasil kerja mitra sebelum konfirmasi.
            </div>
            <button onclick="showKonfirmasi()" class="btn-z-primary" style="margin-top:var(--s4);width:100%;padding:var(--s2) var(--s3);background:var(--green);border:none;color:white;font-weight:800;border-radius:var(--r2);cursor:pointer;">
                <i class="bi bi-check-circle-fill me-2"></i>Konfirmasi Pekerjaan
            </button>
        </div>
    </div>

    {{-- Status Message --}}
    <div id="status-message" style="padding:var(--s2);background:#fff3cd;border-left:3px solid var(--gold);border-radius:var(--r2);font-size:12px;color:var(--text-main);display:none;margin-bottom:var(--s4);">
        <i class="bi bi-info-circle me-2"></i>
        <span id="message-text"></span>
    </div>

</div>

{{-- Modal Konfirmasi --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:var(--r3);border:none;padding:var(--s3);text-align:center;box-shadow:0 20px 40px rgba(0,45,114,0.15);">
            <div style="width:55px;height:55px;border-radius:var(--r2);background:var(--blue-pale);display:flex;align-items:center;justify-content:center;margin:0 auto var(--s3);">
                <i class="bi bi-clipboard-check-fill" style="color:var(--blue-deep);font-size:21px;"></i>
            </div>
            <h6 style="font-weight:800;color:var(--text-main);margin-bottom:var(--s2);">
                Apakah Anda yakin pekerjaan mitra sudah selesai?
            </h6>
            <p style="font-size:12px;color:var(--text-muted);margin-bottom:var(--s4);">
                Silakan cek terlebih dahulu hasil kerjanya. Setelah dikonfirmasi selesai, pembayaran akan diteruskan ke mitra.
            </p>
            <div class="d-flex gap-2">
                <form action="{{ route('pelanggan.order-tracking.belum-selesai') }}" method="POST" class="w-100">
                    @csrf
                    <input type="hidden" name="tracking_id" value="{{ $tracking->id }}">
                    <button type="submit" class="btn-z-ghost w-100" style="padding:var(--s2) var(--s3);color:var(--red);border:1.5px solid var(--red);background:transparent;border-radius:var(--r2);font-weight:800;font-size:13px;cursor:pointer;">
                        <i class="bi bi-x-circle me-1"></i>Belum Selesai
                    </button>
                </form>
                <form action="{{ route('pelanggan.order-tracking.selesai') }}" method="POST" class="w-100">
                    @csrf
                    <input type="hidden" name="tracking_id" value="{{ $tracking->id }}">
                    <button type="submit" class="btn-z-primary w-100" style="padding:var(--s2) var(--s3);background:var(--green);color:white;border:none;border-radius:var(--r2);font-weight:800;font-size:13px;cursor:pointer;">
                        <i class="bi bi-check-circle-fill me-1"></i>Pekerjaan Selesai
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.progress-step-read {
    display: flex;
    gap: var(--s3);
    position: relative;
    margin-bottom: var(--s4);
}

.step-dot-read {
    width: 21px;
    height: 21px;
    border-radius: 50%;
    background: var(--border);
    flex-shrink: 0;
    z-index: 1;
    transition: all 0.3s ease;
}

.step-line-read {
    position: absolute;
    left: 10px;
    top: 21px;
    bottom: 0;
    width: 2px;
    background: var(--border);
    transition: background-color 0.3s ease;
}

.progress-step-read:last-child .step-line-read {
    display: none;
}

.step-content-read {
    flex: 1;
    padding-bottom: var(--s4);
}

/* Active step */
.progress-step-read.active .step-dot-read {
    background: var(--blue-deep);
    box-shadow: 0 0 8px rgba(0,45,114,0.3);
    animation: pulse 2s infinite;
}

.progress-step-read.active .step-dot-read::before {
    content: '';
    position: absolute;
    width: 21px;
    height: 21px;
    border-radius: 50%;
    border: 2px solid var(--blue-deep);
    animation: pulse-ring 2s infinite;
}

/* Completed step */
.progress-step-read.completed .step-dot-read {
    background: var(--green);
    box-shadow: 0 0 8px rgba(16,185,129,0.3);
}

.progress-step-read.completed .step-line-read {
    background: var(--green);
}

.progress-step-read.completed .step-content-read > div {
    color: var(--green) !important;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@keyframes pulse-ring {
    0% {
        box-shadow: 0 0 0 0 rgba(0,45,114,0.7);
    }
    70% {
        box-shadow: 0 0 0 8px rgba(0,45,114,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0,45,114,0);
    }
}

.btn-z-primary {
    background: var(--blue-deep);
    color: white;
    border: none;
    padding: var(--s2) var(--s4);
    border-radius: var(--r2);
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-z-primary:hover {
    background: var(--blue-mid);
    transform: translateY(-2px);
}

.btn-z-ghost {
    background: transparent;
    color: var(--text-main);
    border: 1.5px solid var(--border);
    padding: var(--s2) var(--s4);
    border-radius: var(--r2);
    font-weight: 800;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-z-ghost:hover {
    background: var(--bg);
    border-color: var(--text-muted);
}
</style>

@push('scripts')
<script>
let pollingInterval = null;
const trackingId = {{ $tracking->id }};

function startPolling() {
    console.log('Order tracking polling started');
    pollingInterval = setInterval(pollOrderStatus, 5000);
    pollOrderStatus(); // Check immediately
}

async function pollOrderStatus() {
    try {
        const resp = await fetch(`/order-tracking/${trackingId}/status`);
        const data = await resp.json();

        if (data.success) {
            updateProgress(data.status);
            updateConfirmationSection(data.status);
        }
    } catch (e) {
        console.error('Polling error:', e);
    }
}

function updateProgress(status) {
    const steps = ['menuju_lokasi', 'di_lokasi', 'dikerjakan', 'selesai_mitra'];
    const currentIdx = steps.indexOf(status);

    document.querySelectorAll('.progress-step-read').forEach((step, idx) => {
        step.classList.remove('active', 'completed');
        if (idx < currentIdx) {
            step.classList.add('completed');
        } else if (idx === currentIdx && status !== 'selesai') {
            step.classList.add('active');
        } else if (status === 'selesai') {
            step.classList.add('completed');
        }
    });
}

function updateConfirmationSection(status) {
    const confirmSection = document.getElementById('section-confirm');
    if (status === 'selesai_mitra') {
        confirmSection.style.display = 'block';
    } else if (status === 'belum_selesai') {
        confirmSection.style.display = 'none';
        showStatusMessage('Mitra akan memperbaiki pekerjaannya. Silakan tunggu update selanjutnya.');
    } else if (status === 'selesai') {
        confirmSection.style.display = 'none';
    }
}

function showStatusMessage(message) {
    const msgDiv = document.getElementById('status-message');
    document.getElementById('message-text').textContent = message;
    msgDiv.style.display = 'block';
    setTimeout(() => {
        msgDiv.style.display = 'none';
    }, 5000);
}

function showKonfirmasi() {
    const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
    modal.show();
}

// Initialize on page load
window.addEventListener('load', function() {
    startPolling();
    updateProgress('{{ $tracking->status }}');
    updateConfirmationSection('{{ $tracking->status }}');
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (pollingInterval) clearInterval(pollingInterval);
});
</script>
@endpush

@endsection
