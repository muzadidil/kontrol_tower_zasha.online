@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-users me-2" style="color:#005aa9;"></i>Manajemen Mitra
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Kelola data mitra penyedia layanan.</p>
    </div>
    <a href="{{ route('mitra.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold btn-sm">
        <i class="fas fa-plus me-1"></i>Tambah Mitra
    </a>
</div>

@if(session('success'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="card-zasha card overflow-hidden">
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
        <h6 class="fw-bold mb-0" style="color:#1e293b;">
            Daftar Mitra
            <span class="badge rounded-pill ms-2" style="background:#eff6ff; color:#005aa9; font-size:0.7rem;">
                {{ $mitras->count() }} mitra
            </span>
        </h6>
    </div>

    <div class="table-responsive">
        <table class="table mb-0" style="font-size:0.875rem;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th class="ps-4 py-3" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#6b7280; border-bottom:2px solid #e5e7eb;">#</th>
                    <th class="py-3" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#6b7280; border-bottom:2px solid #e5e7eb;">Nama Mitra</th>
                    <th class="py-3" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#6b7280; border-bottom:2px solid #e5e7eb;">No. WA</th>
                    <th class="py-3" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#6b7280; border-bottom:2px solid #e5e7eb;">Status</th>
                    <th class="py-3" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#6b7280; border-bottom:2px solid #e5e7eb;">Verifikasi</th>
                    <th class="py-3 text-end pe-4" style="font-size:0.72rem; font-weight:700; text-transform:uppercase; color:#6b7280; border-bottom:2px solid #e5e7eb;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mitras as $m)
                <tr style="border-color:#f1f5f9;">
                    <td class="ps-4 py-3 text-muted">{{ $loop->iteration }}</td>
                    <td class="py-3">
                        <div class="fw-semibold" style="color:#1e293b;">{{ $m->nama_panggilan }}</div>
                        @if($m->nama_asli)
                            <div class="text-muted" style="font-size:0.75rem;">{{ $m->nama_asli }}</div>
                        @endif
                    </td>
                    <td class="py-3 text-muted">{{ $m->no_wa ?? '-' }}</td>
                    <td class="py-3">
                        <span class="badge rounded-pill px-3"
                            style="font-size:0.65rem; font-weight:700;
                                   background:{{ $m->status_mitra == 'aktif' ? '#dcfce7' : '#fee2e2' }};
                                   color:{{ $m->status_mitra == 'aktif' ? '#16a34a' : '#dc2626' }};">
                            {{ ucfirst($m->status_mitra ?? 'non-aktif') }}
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="badge rounded-pill px-3"
                            style="font-size:0.65rem; font-weight:700;
                                   background:{{ $m->status_verifikasi == 'Verified' ? '#dbeafe' : '#fef9c3' }};
                                   color:{{ $m->status_verifikasi == 'Verified' ? '#1d4ed8' : '#ca8a04' }};">
                            {{ $m->status_verifikasi ?? 'Pending' }}
                        </span>
                    </td>
                    <td class="py-3 pe-4 text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('mitra.edit', $m->id_mitra) }}"
                               class="btn btn-sm rounded-2 fw-bold"
                               style="background:#eff6ff; color:#005aa9; font-size:0.75rem; padding:4px 10px;">
                                <i class="fas fa-pencil-alt me-1"></i>Edit
                            </a>
                            <form action="{{ route('mitra.destroy', $m->id_mitra) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus mitra {{ $m->nama_panggilan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded-2 fw-bold"
                                        style="background:#fee2e2; color:#dc2626; font-size:0.75rem; padding:4px 10px;">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:#94a3b8;">
                        <i class="fas fa-users fa-2x mb-3 d-block"></i>
                        <div class="fw-semibold">Belum ada data mitra</div>
                        <div style="font-size:0.8rem;">Tambah mitra pertama menggunakan tombol di atas.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
