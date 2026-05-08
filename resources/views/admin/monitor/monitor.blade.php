@extends('layouts.admin') {{-- Sesuaikan dengan nama file layout master Anda --}}
    
@section('content')

{{-- Asset bisa ditaruh di sini atau di push ke stack layout --}}
<link rel="stylesheet" href="{{ asset('assets/css/admin_monitor.css') }}">
<script src="{{ asset('assets/js/admin_monitor.js') }}" defer></script>

<div id="monitor-wrapper" class="animate-in">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-radar text-primary me-2"></i>Radar Mitra ZASHA</h4>
            <small class="text-muted">Memantau "detak jantung" aktivitas teknisi & kurir secara real-time.</small>
        </div>
    </div>

    {{-- 2. NOTIFIKASI FLASH MESSAGE --}}
    @if(session('pesan') == 'berhasil_reset')
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate-in" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <div>
                    <strong class="d-block">Gembok Sesi Dilepas!</strong>
                    <span class="small">Mitra tersebut sekarang sudah bisa login kembali ke aplikasi.</span>
                </div>
            </div>
        </div>
    @endif

    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Mitra / Kontak</th>
                        <th>Status Online</th>
                        <th>Aktivitas</th>
                        <th>Ping Terakhir</th>
                        <th class="text-center">Sesi Gembok</th>
                        <th class="text-end pe-4">Aksi Kontrol</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 3. LOOPING DATA MITRA --}}
                    @forelse($mitras as $mitra)
                        @php
                            $is_online = false;
                            if(!empty($mitra->last_ping)) {
                                $diff = time() - strtotime($mitra->last_ping);
                                if($diff < 300) { $is_online = true; } 
                            }
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $mitra->nama_mitra }}</div>
                                <div class="text-muted small"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $mitra->no_wa }}</div>
                            </td>
                            <td>
                                @if($is_online)
                                    <span class="badge badge-online">
                                        <i class="bi bi-circle-fill me-1 small"></i> AKTIF
                                    </span>
                                @else
                                    <span class="badge badge-offline">
                                        <i class="bi bi-circle-fill me-1 small"></i> OFFLINE
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-status-kerja">
                                    {{ $mitra->status_kerja ?? 'istirahat' }}
                                </span>
                            </td>
                            <td>
                                <div class="ping-time">
                                    {{ $mitra->last_ping ? \Carbon\Carbon::parse($mitra->last_ping)->format('H:i:s') : '-' }}
                                </div>
                                <div class="ping-date">
                                    {{ $mitra->last_ping ? \Carbon\Carbon::parse($mitra->last_ping)->format('d/m/Y') : '' }}
                                </div>
                            </td>
                            <td class="text-center">
                                @if($mitra->token_login)
                                    <code class="token-code">
                                        {{ Str::substr($mitra->token_login, 0, 8) }}...
                                    </code>
                                @else
                                    <span class="text-muted small italic">-</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($mitra->token_login)
                                    {{-- Menggunakan Form POST untuk aksi yang memanipulasi database --}}
                                    <form action="{{ route('admin.monitor.force_logout', $mitra->id_mitra) }}" method="POST" class="d-inline" onsubmit="return confirm('Paksa keluar mitra ini? Mereka harus login ulang nanti.')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm rounded-3 px-3 shadow-sm btn-force">
                                            FORCE LOGOUT
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-light btn-sm rounded-3 px-3 border text-muted btn-stable" disabled>STABLE</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        {{-- Jika tabel database kosong --}}
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-person-x text-muted fs-2 d-block mb-2"></i>
                                <span class="text-muted">Belum ada mitra yang terdaftar di sistem.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 text-center">
        <div class="scan-indicator shadow-sm">
            <span class="spinner-grow spinner-grow-sm text-primary me-2" role="status"></span>
            <small>Radar Status Aktif</small>
        </div>
    </div>
</div>

@endsection