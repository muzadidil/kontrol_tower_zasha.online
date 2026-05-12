@extends('layouts.admin')

@section('content')
@include('admin.partials._zasha-style')

<div class="zasha-page-header">
    <div class="zasha-page-title">
        <h4><i class="bi bi-cash-coin"></i> Konfirmasi Top-Up</h4>
        <div class="zasha-page-subtitle">Validasi bukti transfer mitra & pelanggan sebelum menambah saldo.</div>
    </div>
    <a href="{{ route('admin.finance.topup.index') }}" class="btn btn-light rounded-pill px-3 border small fw-bold">
        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
    </a>
</div>

@if(session('notif'))
    <div class="alert alert-success rounded-3 small">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('notif') }}
    </div>
@endif

<div class="alert alert-warning rounded-3 small mb-3" style="background:#fffbeb; border-color:#fde68a; color:#92400e;">
    <i class="bi bi-info-circle-fill me-1"></i>
    <strong>Penting!</strong> Pastikan dana sudah masuk ke mutasi rekening sebelum menekan <b>TERIMA</b>. Cocokkan <b>nominal transfer unik</b> dengan mutasi bank.
</div>

<div class="zasha-card">
    <div class="zasha-list-header">
        <h6><i class="bi bi-hourglass-split"></i> Antrian Top-Up</h6>
        <span class="badge-count">{{ $topups->count() }}</span>
    </div>

    @forelse($topups as $row)
        @php
            $isMitra = $row->tipe_user == 'mitra';
            $totalTransfer = $row->total_transfer ?? $row->nominal;
        @endphp
        <div class="zasha-row">
            <div class="zasha-row-icon {{ $isMitra ? 'success' : '' }}">
                <i class="bi {{ $isMitra ? 'bi-person-badge' : 'bi-person-circle' }}"></i>
            </div>
            <div class="zasha-row-body">
                <div class="zasha-row-title">
                    {{ $row->nama_user }}
                    <span class="zasha-status-badge {{ $isMitra ? 'success' : 'process' }} ms-1" style="font-size:0.6rem;">
                        {{ $isMitra ? 'Mitra' : 'Pelanggan' }}
                    </span>
                </div>
                <div class="zasha-row-meta">
                    @if($row->no_wa)
                        <span><i class="bi bi-whatsapp text-success"></i> {{ $row->no_wa }}</span>
                    @endif
                    @if($row->total_transfer && $row->kode_unik)
                        <span>Rp {{ number_format($row->nominal, 0, ',', '.') }} + Kode <strong style="color:#e65100;">{{ $row->kode_unik }}</strong></span>
                    @endif
                    @if(!empty($row->bank_tujuan))
                        <span><i class="bi bi-bank"></i> {{ $row->bank_tujuan }}</span>
                    @endif
                    <span><i class="bi bi-clock"></i> {{ date('d M, H:i', strtotime($row->waktu)) }} WIB</span>
                </div>
            </div>
            <div class="zasha-row-amount">
                <div class="zasha-row-amount-main">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</div>
                <div class="zasha-row-amount-sub">Total Transfer</div>
            </div>
            <div class="d-flex flex-column gap-1 flex-shrink-0" style="min-width:80px;">
                @php
                    $routeKey = $isMitra ? 'admin.finance.topup.mitra' : 'admin.finance.topup.pelanggan';
                    $confirmMsg = "Konfirmasi Saldo Rp " . number_format($row->nominal, 0, ',', '.') . " untuk " . strtoupper($isMitra ? 'mitra' : 'pelanggan') . " " . $row->nama_user . "?\\n\\nCocokkan transfer Rp " . number_format($totalTransfer, 0, ',', '.') . " di mutasi bank.";
                @endphp
                <a href="{{ route($routeKey, ['id' => $row->id, 'aksi' => 'setuju']) }}"
                   onclick="return confirm('{{ $confirmMsg }}')"
                   class="btn btn-success btn-sm rounded-pill fw-bold" style="padding:5px 14px; font-size:0.7rem;">
                    <i class="bi bi-check-lg"></i> TERIMA
                </a>
                <a href="{{ route($routeKey, ['id' => $row->id, 'aksi' => 'tolak']) }}"
                   onclick="return confirm('Yakin ingin TOLAK top-up ini?')"
                   class="btn btn-outline-danger btn-sm rounded-pill fw-bold" style="padding:5px 14px; font-size:0.7rem;">
                    TOLAK
                </a>
            </div>
        </div>
    @empty
        <div class="zasha-empty">
            <i class="bi bi-check2-all"></i>
            <div class="zasha-empty-title">Bersih! Tidak ada antrian top-up.</div>
            <div class="zasha-empty-sub">Semua permintaan sudah diproses.</div>
        </div>
    @endforelse
</div>
@endsection
