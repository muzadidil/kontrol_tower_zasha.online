@extends('layouts.admin')

@section('content')
<style>
    .table-zasha thead th { background: #f8fafc; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 2px solid #e5e7eb; padding: 12px 16px; }
    .table-zasha tbody td { padding: 14px 16px; vertical-align: middle; border-color: #f1f5f9; }
    .table-zasha tbody tr:hover { background: #f8fafc; }
    .avatar-initials { width: 38px; height: 38px; border-radius: 10px; background: #eff6ff; color: #005aa9; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .badge-pending { background: #fef9c3; color: #ca8a04; font-size: 0.65rem; font-weight: 700; padding: 4px 10px; border-radius: 50px; }
    .nominal-wd { font-size: 1rem; font-weight: 800; color: #dc2626; }
    .bank-info { font-size: 0.78rem; color: #64748b; }
    .btn-approve { background: #16a34a; color: white; border: none; border-radius: 8px; padding: 6px 16px; font-size: 0.78rem; font-weight: 700; transition: .15s; }
    .btn-approve:hover { background: #15803d; color: white; }
    .btn-reject { background: white; color: #dc2626; border: 1px solid #fca5a5; border-radius: 8px; padding: 6px 16px; font-size: 0.78rem; font-weight: 700; transition: .15s; }
    .btn-reject:hover { background: #fef2f2; color: #dc2626; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-money-bill-wave me-2" style="color:#005aa9;"></i>Approval Penarikan
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Proses permintaan penarikan saldo dari mitra.</p>
    </div>
    <span class="badge rounded-pill px-3 py-2" style="background:#fef9c3; color:#ca8a04; font-size:0.78rem;">
        <i class="fas fa-clock me-1"></i>{{ $withdrawals->count() }} Menunggu
    </span>
</div>

@if(session('success'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#fef2f2; color:#dc2626; font-size:0.875rem;">
        <i class="fas fa-exclamation-circle fs-5"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="card border-0 rounded-4 overflow-hidden" style="box-shadow:0 2px 8px rgba(0,0,0,0.06);">
    <div class="table-responsive">
        <table class="table table-zasha mb-0">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th>Mitra</th>
                    <th>Rekening Tujuan</th>
                    <th>Nominal</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $i => $wd)
                <tr>
                    <td class="text-muted" style="font-size:0.78rem;">{{ $i + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-initials">
                                {{ strtoupper(substr($wd->mitra->nama_panggilan ?? 'MT', 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-semibold" style="font-size:0.875rem; color:#1e293b;">
                                    {{ $wd->mitra->nama_panggilan ?? 'N/A' }}
                                </div>
                                <div class="bank-info">ID #{{ $wd->mitra_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold" style="font-size:0.875rem; color:#1e293b;">{{ $wd->bank_name }}</div>
                        <div class="bank-info">{{ $wd->account_number }} &bull; {{ $wd->account_name }}</div>
                    </td>
                    <td>
                        <div class="nominal-wd">Rp {{ number_format($wd->nominal, 0, ',', '.') }}</div>
                        @if($wd->created_at)
                            <div class="bank-info">{{ \Carbon\Carbon::parse($wd->created_at)->format('d M Y, H:i') }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-pending">{{ ucfirst($wd->status) }}</span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <form action="{{ route('admin.finance.withdrawal.approve', $wd->id) }}" method="POST"
                                  onsubmit="return confirm('Setujui penarikan Rp {{ number_format($wd->nominal, 0, ',', '.') }} untuk {{ $wd->mitra->nama_panggilan ?? '' }}?')">
                                @csrf
                                <button type="submit" class="btn-approve">
                                    <i class="fas fa-check me-1"></i>Setujui
                                </button>
                            </form>
                            <form action="{{ route('admin.finance.withdrawal.reject', $wd->id) }}" method="POST"
                                  onsubmit="return confirm('Tolak penarikan ini? Saldo akan dikembalikan.')">
                                @csrf
                                <button type="submit" class="btn-reject">
                                    <i class="fas fa-times me-1"></i>Tolak
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div style="color:#94a3b8;">
                            <i class="fas fa-check-double fa-2x mb-3 d-block"></i>
                            <div class="fw-semibold">Tidak ada permintaan penarikan</div>
                            <div style="font-size:0.8rem;">Semua permintaan sudah diproses.</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
