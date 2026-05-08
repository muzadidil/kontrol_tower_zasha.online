@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/verifikasi_all.css') }}">
<script src="{{ asset('assets/js/verifikasi_all.js') }}" defer></script>

<div id="verifikasi-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-shield-lock-fill text-primary me-2"></i>Unified Control Tower</h4>
            <small class="text-muted">Kelola pengajuan perubahan data & pendaftaran mitra baru.</small>
        </div>
        <a href="{{ route('admin.verification.index') }}" class="btn btn-white shadow-sm rounded-pill px-3 border bg-white btn-sm fw-bold">
            <i class="bi bi-arrow-clockwise"></i> Refresh Feed
        </a>
    </div>

    @if(session('notif_verif'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate-in">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('notif_verif') }}
        </div>
    @endif

    <div class="card border-0 bg-dark text-white rounded-4 p-4 mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary p-3 rounded-circle me-3">
                <i class="bi bi-eye-fill fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1">Double Check System</h6>
                <p class="small mb-0 opacity-75">Periksa baris berwarna hijau (Data Baru). Pastikan sesuai dengan dokumen yang dikirim mitra di WhatsApp.</p>
            </div>
        </div>
    </div>

    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Pihak Terkait</th>
                        <th>Perubahan / Detail Pengajuan</th>
                        <th>Status / Unit</th>
                        <th class="text-center pe-4">Aksi Final</th>
                    </tr>
                </thead>
                <tbody>
                    @if($list_driver->isEmpty() && $list_mitra->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-cup-hot text-muted display-2 d-block mb-3 opacity-25"></i>
                                    <h6 class="text-muted fw-bold">Semua Aman Muz!</h6>
                                    <small class="text-muted">Tidak ada pengajuan verifikasi yang tertunda saat ini.</small>
                                </div>
                            </td>
                        </tr>
                    @endif

                    @foreach($list_driver as $r)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $r->nama_driver }}</div>
                            <span class="type-badge badge-driver mt-1 d-inline-block">DRIVER JASTIP</span>
                        </td>
                        <td>
                            <div class="diff-box">
                                @if(!empty($r->nama_asli_baru))
                                    <span class="old-data">{{ $r->nama_asli }}</span>
                                    <span class="new-data"><i class="bi bi-arrow-right me-1"></i>{{ $r->nama_asli_baru }}</span>
                                @else
                                    <span class="fw-bold text-dark">{{ $r->nama_asli }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="small fw-bold">Plat: {{ $r->plat_nomor }}</div>
                            <div class="text-muted" style="font-size: 10px;"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $r->no_wa }}</div>
                        </td>
                        <td class="text-center pe-4">
                            <form action="{{ route('admin.verification.approve') }}" method="POST" onsubmit="return confirm('Yakin setujui perubahan data untuk {{ $r->nama_driver }}?')">
                                @csrf
                                <input type="hidden" name="target_id" value="{{ $r->id_driver }}">
                                <input type="hidden" name="account_type" value="driver">
                                <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm btn-approve">
                                    APPROVE
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach

                    @foreach($list_mitra as $m)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">{{ $m->nama_mitra ?? 'Unknown' }}</div>
                            <span class="type-badge badge-mitra mt-1 d-inline-block">MITRA LAYANAN</span>
                        </td>
                        <td>
                            <div class="diff-box">
                                @if(!empty($m->no_wa_baru))
                                    <span class="old-data">WA: {{ $m->no_wa }}</span>
                                    <span class="new-data">WA: {{ $m->no_wa_baru }}</span>
                                @else
                                    <div class="small text-muted mb-1">Update Kendaraan/Alamat</div>
                                    <span class="fw-bold text-dark">{{ $m->plat_nomor }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if(!empty($m->plat_pengajuan))
                                <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size: 8px;">PERUBAHAN PLAT</span>
                            @else
                                <div class="fw-bold text-secondary" style="font-size: 11px;">{{ $m->jenis_kendaraan }}</div>
                            @endif
                            <div class="text-muted" style="font-size: 10px;">{{ $m->no_wa }}</div>
                        </td>
                        <td class="text-center pe-4">
                            <form action="{{ route('admin.verification.approve') }}" method="POST" onsubmit="return confirm('Konfirmasi verifikasi data mitra {{ $m->nama_mitra }}?')">
                                @csrf
                                <input type="hidden" name="target_id" value="{{ $m->id_mitra }}">
                                <input type="hidden" name="account_type" value="mitra">
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm btn-approve">
                                    SETUJUI
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
