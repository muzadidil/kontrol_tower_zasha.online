@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/topup.css') }}">
<script src="{{ asset('assets/js/topup.js') }}" defer></script>

<div id="topup-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-cash-coin text-primary me-2"></i>Konfirmasi Top-Up</h4>
            <small class="text-muted">Validasi bukti transfer mitra & pelanggan sebelum menambah saldo.</small>
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
                Cocokkan <b>nominal transfer unik</b> dengan mutasi bank.
            </div>
        </div>
    </div>

    <div class="card card-zasha overflow-hidden">
        <div class="table-responsive">
            <table class="table table-zasha table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tipe</th>
                        <th>Pengirim</th>
                        <th>Nominal Transfer</th>
                        <th>Waktu Pengajuan</th>
                        <th class="text-center pe-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topups as $row)
                        <tr>
                            <td class="ps-4">
                                @if($row->tipe_user == 'mitra')
                                    <span class="badge rounded-pill bg-success" style="font-size:0.7rem;">
                                        <i class="bi bi-person-badge me-1"></i>Mitra
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-primary" style="font-size:0.7rem;">
                                        <i class="bi bi-person me-1"></i>Pelanggan
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->nama_user }}</div>
                                @if($row->no_wa)
                                    <div class="text-muted small"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $row->no_wa }}</div>
                                @endif
                            </td>
                            <td>
                                @if($row->total_transfer && $row->kode_unik)
                                    <div class="nominal-topup fw-bold" style="font-size:1.05rem; color:#005aa9;">
                                        Rp {{ number_format($row->total_transfer, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;">
                                        Nominal: Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                        + Kode Unik: <strong style="color:#e65100;">{{ $row->kode_unik }}</strong>
                                    </small><br>
                                    <small class="text-muted" style="font-size: 10px;">
                                        <i class="bi bi-bank me-1"></i>Via {{ $row->bank_tujuan ?? 'Transfer Bank' }}
                                    </small>
                                @else
                                    <div class="nominal-topup">
                                        Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;">Metode: Transfer Bank</small>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-secondary small">{{ date('H:i', strtotime($row->waktu)) }} WIB</div>
                                <div class="text-muted" style="font-size: 10px;">{{ date('d M Y', strtotime($row->waktu)) }}</div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2">
                                    @if($row->tipe_user == 'mitra')
                                        <a href="{{ route('admin.finance.topup.mitra', ['id' => $row->id, 'aksi' => 'setuju']) }}" 
                                           onclick="return confirm('Konfirmasi Saldo Rp {{ number_format($row->nominal, 0, ',', '.') }} untuk MITRA {{ $row->nama_user }}?\n\nCocokkan transfer Rp {{ number_format($row->total_transfer ?? $row->nominal, 0, ',', '.') }} di mutasi bank.')"
                                           class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                            <i class="bi bi-check-lg me-1"></i> TERIMA
                                        </a>
                                        <a href="{{ route('admin.finance.topup.mitra', ['id' => $row->id, 'aksi' => 'tolak']) }}" 
                                           onclick="return confirm('Yakin ingin TOLAK top-up mitra ini?')"
                                           class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                            TOLAK
                                        </a>
                                    @else
                                        <a href="{{ route('admin.finance.topup.pelanggan', ['id' => $row->id, 'aksi' => 'setuju']) }}" 
                                           onclick="return confirm('Konfirmasi Saldo Rp {{ number_format($row->nominal, 0, ',', '.') }} untuk PELANGGAN {{ $row->nama_user }}?\n\nCocokkan transfer Rp {{ number_format($row->total_transfer ?? $row->nominal, 0, ',', '.') }} di mutasi bank.')"
                                           class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                            <i class="bi bi-check-lg me-1"></i> TERIMA
                                        </a>
                                        <a href="{{ route('admin.finance.topup.pelanggan', ['id' => $row->id, 'aksi' => 'tolak']) }}" 
                                           onclick="return confirm('Yakin ingin TOLAK top-up pelanggan ini?')"
                                           class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                            TOLAK
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
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
