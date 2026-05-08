@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/topup.css') }}">
<script src="{{ asset('assets/js/topup.js') }}" defer></script>

<div id="topup-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-cash-coin text-primary me-2"></i>Konfirmasi Top-Up</h4>
            <small class="text-muted">Validasi bukti transfer mitra sebelum menambah saldo.</small>
        </div>
        <a href="{{ route('admin.finance.topup.index') }}" class="btn btn-white shadow-sm rounded-pill px-3 border bg-white btn-sm">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </a>
    </div>

    @if(session('notif'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate-in">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('notif') }}
        </div>
    @endif

    <div class="alert alert-topup-info border-0 shadow-sm rounded-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <strong>Penting!</strong> Pastikan dana sudah benar-benar masuk ke mutasi rekening ZASHA sebelum menekan tombol <b>TERIMA</b>.
            </div>
        </div>
    </div>

    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Mitra / Pengirim</th>
                        <th>Nominal Transfer</th>
                        <th>Waktu Pengajuan</th>
                        <th class="text-center pe-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topups as $row)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $row->nama_mitra }}</div>
                                <div class="text-muted small"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $row->no_wa }}</div>
                            </td>
                            <td>
                                <div class="nominal-topup">
                                    Rp {{ number_format($row->jumlah_topup, 0, ',', '.') }}
                                </div>
                                <small class="text-muted" style="font-size: 10px;">Metode: Transfer Bank</small>
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary small">{{ date('H:i', strtotime($row->tanggal)) }} WIB</div>
                                <div class="text-muted" style="font-size: 10px;">{{ date('d M Y', strtotime($row->tanggal)) }}</div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.finance.topup.process', ['id' => $row->id_topup, 'aksi' => 'setuju']) }}" 
                                       onclick="return confirm('Konfirmasi Saldo Rp {{ number_format($row->jumlah_topup, 0, ',', '.') }} untuk {{ $row->nama_mitra }}?')"
                                       class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                        <i class="bi bi-check-lg me-1"></i> TERIMA
                                    </a>

                                    <a href="{{ route('admin.finance.topup.process', ['id' => $row->id_topup, 'aksi' => 'tolak']) }}" 
                                       onclick="return confirm('Yakin ingin membatalkan (Tolak) top-up ini?')"
                                       class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                        TOLAK
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4 opacity-50">
                                    <i class="bi bi-check2-all display-1 d-block mb-3"></i>
                                    <h6 class="fw-bold">Bersih! Tidak ada antrian top-up.</h6>
                                    <small>Semua permintaan sudah diproses.</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
