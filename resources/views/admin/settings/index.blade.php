@extends('layouts.admin')
@section('title', 'Pengaturan Sistem')

@section('content')
<style>
    .setting-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 20px; }
    .nav-tabs-zasha { border-bottom: 2px solid #f0f0f0; margin-bottom: 0; }
    .nav-tabs-zasha .nav-link {
        border: none;
        padding: 12px 20px;
        font-weight: 500;
        color: #6c757d;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs-zasha .nav-link.active {
        color: #f0a500;
        border-bottom-color: #f0a500;
        background: transparent;
    }
    .input-secret { font-family: monospace; }
    .form-section { padding: 24px; }
    .legal-textarea { font-family: 'Courier New', monospace; font-size: 13px; }
</style>

<div class="container-fluid py-4" style="max-width: 1100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-cog me-2 text-warning"></i>Pengaturan Sistem</h4>
            <p class="text-muted mb-0 small">Kelola konfigurasi API, konten legal, dan parameter operasional aplikasi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle me-2"></i>
            @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="setting-card">
        <ul class="nav nav-tabs nav-tabs-zasha px-3 pt-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'api' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-api">
                    <i class="fas fa-key me-1"></i> API Integration
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'operasional' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-operasional">
                    <i class="fas fa-sliders-h me-1"></i> Operasional
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'legal' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-legal">
                    <i class="fas fa-file-contract me-1"></i> Konten Legal & FAQ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'about' ? 'active' : '' }}" data-bs-toggle="tab" href="#tab-about">
                    <i class="fas fa-info-circle me-1"></i> Info Aplikasi
                </a>
            </li>
        </ul>

        <div class="tab-content">
            {{-- ── TAB 1: API INTEGRATION ── --}}
            <div class="tab-pane fade {{ $tab === 'api' ? 'show active' : '' }}" id="tab-api">
                <form action="{{ route('admin.settings.api') }}" method="POST" class="form-section">
                    @csrf
                    <h5 class="fw-bold mb-3"><i class="fas fa-map-marker-alt text-info me-2"></i>Google Maps</h5>
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">API Key</label>
                        <input type="text" name="google_maps_api_key" class="form-control input-secret" value="{{ $settings['google_maps_api_key'] }}" placeholder="AIzaSy...">
                        <div class="form-text">Untuk pin alamat & maps. <a href="https://console.cloud.google.com" target="_blank">Dapatkan key</a></div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3"><i class="fas fa-credit-card text-success me-2"></i>Tokopay (Topup Saldo)</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Merchant ID</label>
                            <input type="text" name="tokopay_merchant_id" class="form-control" value="{{ $settings['tokopay_merchant_id'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Secret Key</label>
                            <input type="password" name="tokopay_secret" class="form-control input-secret" value="{{ $settings['tokopay_secret'] }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">API URL</label>
                            <input type="url" name="tokopay_url" class="form-control" value="{{ $settings['tokopay_url'] }}">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info small mb-0">
                                <i class="fas fa-link me-1"></i> <strong>Webhook URL:</strong>
                                <code>{{ url('/webhook/tokopay') }}</code>
                                <br>Set URL ini di dashboard Tokopay agar status pembayaran auto-update.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3"><i class="fas fa-mobile-alt text-primary me-2"></i>Digiflazz (PPOB - Pulsa, Paket, Token)</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Username</label>
                            <input type="text" name="digiflazz_username" class="form-control" value="{{ $settings['digiflazz_username'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">API Key</label>
                            <input type="password" name="digiflazz_api_key" class="form-control input-secret" value="{{ $settings['digiflazz_api_key'] }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Webhook Secret</label>
                            <input type="password" name="digiflazz_webhook_secret" class="form-control input-secret" value="{{ $settings['digiflazz_webhook_secret'] }}">
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info small mb-0">
                                <i class="fas fa-link me-1"></i> <strong>Webhook URL:</strong>
                                <code>{{ url('/webhook/digiflazz') }}</code>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold mb-3"><i class="fas fa-bell text-danger me-2"></i>Firebase Cloud Messaging (Push Notification)</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Project ID</label>
                            <input type="text" name="fcm_project_id" class="form-control" value="{{ $settings['fcm_project_id'] }}" placeholder="zasha-tower">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Server Key</label>
                            <input type="password" name="fcm_server_key" class="form-control input-secret" value="{{ $settings['fcm_server_key'] }}">
                        </div>
                        <div class="col-12">
                            <div class="form-text">Untuk push notification ke aplikasi mobile (Flutter). <a href="https://console.firebase.google.com" target="_blank">Buka Firebase Console</a></div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan Konfigurasi API</button>
                </form>
            </div>

            {{-- ── TAB 2: OPERASIONAL ── --}}
            <div class="tab-pane fade {{ $tab === 'operasional' ? 'show active' : '' }}" id="tab-operasional">
                <form action="{{ route('admin.settings.operasional') }}" method="POST" class="form-section">
                    @csrf
                    <h5 class="fw-bold mb-3"><i class="fas fa-percentage text-warning me-2"></i>Komisi & Limit Transaksi</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Komisi Zasha Default (%)</label>
                            <input type="number" name="komisi_default" class="form-control" step="0.1" min="0" max="100" value="{{ $settings['komisi_default'] }}">
                            <div class="form-text">Persentase komisi default tiap transaksi.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Min. Withdraw Mitra (Rp)</label>
                            <input type="number" name="min_withdraw_mitra" class="form-control" min="0" step="1000" value="{{ $settings['min_withdraw_mitra'] }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Min. Topup Saldo (Rp)</label>
                            <input type="number" name="min_topup" class="form-control" min="0" step="1000" value="{{ $settings['min_topup'] }}">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-warning fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan</button>
                </form>
            </div>

            {{-- ── TAB 3: KONTEN LEGAL & FAQ ── --}}
            <div class="tab-pane fade {{ $tab === 'legal' ? 'show active' : '' }}" id="tab-legal">
                <form action="{{ route('admin.settings.legal') }}" method="POST" class="form-section">
                    @csrf

                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#sub-tos-mitra">TOS Mitra</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sub-tos-pelanggan">TOS Pelanggan</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sub-privacy">Privacy Policy</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#sub-faq">FAQ</a></li>
                    </ul>

                    <div class="tab-content mb-4">
                        <div class="tab-pane fade show active" id="sub-tos-mitra">
                            <label class="form-label small fw-semibold">Term of Service Mitra</label>
                            <textarea name="tos_mitra" class="form-control legal-textarea" rows="14" placeholder="Tulis syarat & ketentuan untuk mitra...">{{ $settings['tos_mitra'] }}</textarea>
                        </div>
                        <div class="tab-pane fade" id="sub-tos-pelanggan">
                            <label class="form-label small fw-semibold">Term of Service Pelanggan</label>
                            <textarea name="tos_pelanggan" class="form-control legal-textarea" rows="14" placeholder="Tulis syarat & ketentuan untuk pelanggan...">{{ $settings['tos_pelanggan'] }}</textarea>
                        </div>
                        <div class="tab-pane fade" id="sub-privacy">
                            <label class="form-label small fw-semibold">Privacy Policy</label>
                            <textarea name="privacy_policy" class="form-control legal-textarea" rows="14" placeholder="Tulis kebijakan privasi...">{{ $settings['privacy_policy'] }}</textarea>
                        </div>
                        <div class="tab-pane fade" id="sub-faq">
                            <label class="form-label small fw-semibold">Konten FAQ</label>
                            <textarea name="faq_content" class="form-control legal-textarea" rows="14" placeholder="## Pertanyaan 1&#10;Jawaban...&#10;&#10;## Pertanyaan 2&#10;Jawaban...">{{ $settings['faq_content'] }}</textarea>
                            <div class="form-text">Gunakan format Markdown (##) untuk header pertanyaan.</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan Konten Legal</button>
                </form>
            </div>

            {{-- ── TAB 4: INFO APLIKASI ── --}}
            <div class="tab-pane fade {{ $tab === 'about' ? 'show active' : '' }}" id="tab-about">
                <form action="{{ route('admin.settings.about') }}" method="POST" class="form-section">
                    @csrf

                    <h5 class="fw-bold mb-3"><i class="fas fa-id-card text-primary me-2"></i>Identitas Aplikasi</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nama Aplikasi <span class="text-danger">*</span></label>
                            <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tagline / Slogan</label>
                            <input type="text" name="app_tagline" class="form-control" value="{{ $settings['app_tagline'] }}" placeholder="Mis: Solusi Jasa Terpercaya">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">URL Logo</label>
                            <input type="text" name="app_logo_url" class="form-control" value="{{ $settings['app_logo_url'] }}" placeholder="https://... atau /storage/logo.png">
                        </div>
                    </div>

                    <h5 class="fw-bold mb-3"><i class="fas fa-address-book text-success me-2"></i>Kontak Customer Service</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nomor WhatsApp CS</label>
                            <input type="text" name="app_kontak_wa" class="form-control" value="{{ $settings['app_kontak_wa'] }}" placeholder="+628123456789">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email CS</label>
                            <input type="email" name="app_kontak_email" class="form-control" value="{{ $settings['app_kontak_email'] }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Alamat Kantor</label>
                            <textarea name="app_alamat" class="form-control" rows="3">{{ $settings['app_alamat'] }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
