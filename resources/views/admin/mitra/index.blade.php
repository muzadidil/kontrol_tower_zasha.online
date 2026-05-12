@extends('layouts.admin')

@section('content')
<style>
    .tab-pill { border: none; background: none; padding: 7px 16px; border-radius: 50px; font-weight: 700; font-size: 0.78rem; color: #64748b; cursor: pointer; transition: .15s; white-space: nowrap; }
    .tab-pill.active { background: #005aa9; color: white; }
    .tab-pill:hover:not(.active) { background: #f1f5f9; color: #1e293b; }
    .tab-bar { background: #f1f5f9; border-radius: 50px; display: inline-flex; padding: 4px; flex-wrap: nowrap; overflow-x: auto; max-width: 100%; }
    .table-adm thead th { background: #f8fafc; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; padding: 11px 16px; }
    .table-adm tbody td { padding: 12px 16px; vertical-align: middle; border-color: #f1f5f9; font-size: 0.875rem; }
    .table-adm tbody tr:hover { background: #f8fafc; }
    .btn-tbl { display: inline-flex; align-items: center; gap: 4px; padding: 4px 11px; border-radius: 7px; font-size: 0.73rem; font-weight: 700; border: none; cursor: pointer; }
    .form-label-up { font-size: 0.72rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 5px; }
    .form-control, .form-select { border-color: #e5e7eb; border-radius: 8px; font-size: 0.875rem; }
    .form-control:focus, .form-select:focus { border-color: #005aa9; box-shadow: 0 0 0 3px rgba(0,90,169,0.08); }
    .kat-item { border: 1px solid #e5e7eb; border-radius: 12px; padding: 13px; margin-bottom: 9px; background: #fff; }
    .kat-icon-box { width: 44px; height: 44px; border-radius: 11px; background: #eff6ff; color: #005aa9; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
    .badge-field { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 600; font-size: 0.65rem; padding: 2px 7px; border-radius: 50px; margin: 2px; display: inline-block; }
    .stat-card { border-radius: 14px; padding: 16px 20px; }
    .diff-box .old-data { display: block; text-decoration: line-through; color: #ef4444; font-size: 0.78rem; }
    .diff-box .new-data { display: block; color: #16a34a; font-size: 0.78rem; font-weight: 700; }
    .type-badge { font-size: 0.6rem; font-weight: 800; padding: 2px 8px; border-radius: 50px; }
    .badge-driver { background: #fef3c7; color: #d97706; }
    .badge-mitra  { background: #dbeafe; color: #1d4ed8; }
</style>

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;"><i class="fas fa-users me-2" style="color:#005aa9;"></i>Pusat Manajemen Mitra</h4>
        <p class="text-muted mb-0" style="font-size:0.82rem;">Kelola mitra, kategori, verifikasi, radar, dan jastip dalam satu tempat.</p>
    </div>
</div>

@if(session('success') || session('pesan') || session('notif_verif'))
<div class="alert border-0 rounded-3 mb-3 d-flex align-items-center gap-2" style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
    <i class="fas fa-check-circle fs-5"></i>
    <span>{{ session('success') ?? session('pesan') ?? session('notif_verif') }}</span>
</div>
@endif

{{-- Tab Bar (tab Kategori dihilangkan: digantikan halaman Role & Akses) --}}
<div class="tab-bar mb-4">
    <button class="tab-pill active" onclick="switchTab('mitra',this)"><i class="fas fa-users me-1"></i>Daftar Mitra <span class="ms-1" style="opacity:.7;font-size:.65rem;">{{ $mitras->count() }}</span></button>
    <a href="{{ route('admin.roles.index') }}" class="tab-pill" style="text-decoration:none;"><i class="fas fa-shield-alt me-1"></i>Role & Akses</a>
    <button class="tab-pill" onclick="switchTab('verifikasi',this)"><i class="fas fa-shield-alt me-1"></i>Verifikasi <span class="ms-1" style="opacity:.7;font-size:.65rem;">{{ $list_driver->count() + $list_mitra_verif->count() }}</span></button>
    <button class="tab-pill" onclick="switchTab('radar',this)"><i class="fas fa-broadcast-tower me-1"></i>Radar</button>
    <button class="tab-pill" onclick="switchTab('jastip',this)"><i class="fas fa-motorcycle me-1"></i>Jastip</button>
</div>

{{-- ======== TAB: DAFTAR MITRA ======== --}}
<div id="tab-mitra">
    <div class="card-zasha card overflow-hidden">
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
            <h6 class="fw-bold mb-0" style="color:#1e293b;">Daftar Mitra Terdaftar</h6>
            <a href="{{ route('admin.mitra.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold btn-sm">
                <i class="fas fa-plus me-1"></i>Tambah Mitra
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-adm mb-0">
                <thead><tr>
                    <th class="ps-4">#</th><th>Nama Mitra</th><th>No. WA</th>
                    <th class="text-center">Status</th><th class="text-center">Verifikasi</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($mitras as $m)
                    <tr>
                        <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold" style="color:#1e293b;">{{ $m->nama_panggilan }}</div>
                            @if($m->nama_asli)<div class="text-muted" style="font-size:.75rem;">{{ $m->nama_asli }}</div>@endif
                        </td>
                        <td class="text-muted">{{ $m->no_wa ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3" style="font-size:.65rem;font-weight:700;background:{{ $m->status_mitra=='aktif'?'#dcfce7':'#fee2e2' }};color:{{ $m->status_mitra=='aktif'?'#16a34a':'#dc2626' }};">
                                {{ ucfirst($m->status_mitra ?? 'non-aktif') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3" style="font-size:.65rem;font-weight:700;background:{{ $m->status_verifikasi=='Verified'?'#dbeafe':'#fef9c3' }};color:{{ $m->status_verifikasi=='Verified'?'#1d4ed8':'#ca8a04' }};">
                                {{ $m->status_verifikasi ?? 'Pending' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.mitra.edit', $m->id_mitra) }}" class="btn-tbl" style="background:#eff6ff;color:#005aa9;"><i class="fas fa-pencil-alt"></i>Edit</a>
                                <form action="{{ route('admin.mitra.destroy', $m->id_mitra) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus mitra {{ $m->nama_panggilan }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-tbl" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-trash"></i>Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5" style="color:#94a3b8;"><i class="fas fa-users fa-2x mb-2 d-block"></i>Belum ada data mitra</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


{{-- ======== TAB: VERIFIKASI ======== --}}
<div id="tab-verifikasi" style="display:none;">
    <div class="card-zasha card overflow-hidden">
        <div class="d-flex align-items-center gap-3 px-4 py-3 border-bottom">
            <h6 class="fw-bold mb-0" style="color:#1e293b;">Pengajuan Verifikasi</h6>
            <span class="badge rounded-pill" style="background:#fee2e2;color:#dc2626;font-size:.7rem;">
                {{ $list_driver->count() + $list_mitra_verif->count() }} pending
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-adm mb-0">
                <thead><tr>
                    <th class="ps-4">Pihak Terkait</th><th>Perubahan</th><th>Info</th>
                    <th class="text-center pe-4">Aksi</th>
                </tr></thead>
                <tbody>
                    @if($list_driver->isEmpty() && $list_mitra_verif->isEmpty())
                    <tr><td colspan="4" class="text-center py-5" style="color:#94a3b8;">
                        <i class="fas fa-coffee fa-2x mb-2 d-block"></i>Tidak ada pengajuan verifikasi saat ini.
                    </td></tr>
                    @endif
                    @foreach($list_driver as $r)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold" style="color:#1e293b;">{{ $r->nama_panggilan }}</div>
                            <span class="type-badge badge-driver">DRIVER JASTIP</span>
                        </td>
                        <td>
                            <div class="diff-box">
                                @if(!empty($r->nama_asli_baru))
                                    <span class="old-data">{{ $r->nama_asli }}</span>
                                    <span class="new-data"><i class="fas fa-arrow-right me-1"></i>{{ $r->nama_asli_baru }}</span>
                                @else
                                    <span style="font-size:.82rem;">{{ $r->nama_asli }}</span>
                                @endif
                            </div>
                        </td>
                        <td><div class="small fw-bold">Plat: {{ $r->plat_nomor }}</div><div class="text-muted" style="font-size:.72rem;">{{ $r->no_wa }}</div></td>
                        <td class="text-center pe-4">
                            <form action="{{ route('admin.verification.approve') }}" method="POST" onsubmit="return confirm('Setujui?')">
                                @csrf
                                <input type="hidden" name="target_id" value="{{ $r->id_mitra }}">
                                <input type="hidden" name="account_type" value="driver">
                                <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold">Approve</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @foreach($list_mitra_verif as $m)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-semibold" style="color:#1e293b;">{{ $m->nama_panggilan ?? '-' }}</div>
                            <span class="type-badge badge-mitra">MITRA LAYANAN</span>
                        </td>
                        <td>
                            <div class="diff-box">
                                @if(!empty($m->no_wa_baru))
                                    <span class="old-data">WA: {{ $m->no_wa }}</span>
                                    <span class="new-data">WA: {{ $m->no_wa_baru }}</span>
                                @else
                                    <span style="font-size:.82rem;">Update kendaraan/alamat</span>
                                @endif
                            </div>
                        </td>
                        <td><div class="text-muted" style="font-size:.72rem;">{{ $m->no_wa }}</div></td>
                        <td class="text-center pe-4">
                            <form action="{{ route('admin.verification.approve') }}" method="POST" onsubmit="return confirm('Setujui?')">
                                @csrf
                                <input type="hidden" name="target_id" value="{{ $m->id_mitra }}">
                                <input type="hidden" name="account_type" value="mitra">
                                <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">Setujui</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ======== TAB: RADAR ======== --}}
<div id="tab-radar" style="display:none;">
    <div class="card-zasha card overflow-hidden">
        <div class="px-4 py-3 border-bottom"><h6 class="fw-bold mb-0" style="color:#1e293b;"><i class="fas fa-broadcast-tower me-2" style="color:#005aa9;"></i>Radar Status Mitra</h6></div>
        <div class="table-responsive">
            <table class="table table-adm mb-0">
                <thead><tr>
                    <th class="ps-4">Mitra</th><th>No. WA</th><th class="text-center">Status Kerja</th><th class="text-center">Verifikasi</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr></thead>
                <tbody>
                    @forelse($radar_mitras as $m)
                    <tr>
                        <td class="ps-4"><div class="fw-semibold" style="color:#1e293b;">{{ $m->nama_panggilan }}</div></td>
                        <td class="text-muted">{{ $m->no_wa ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3" style="font-size:.65rem;font-weight:700;background:{{ $m->status_mitra=='aktif'?'#dcfce7':'#f1f5f9' }};color:{{ $m->status_mitra=='aktif'?'#16a34a':'#64748b' }};">
                                {{ ucfirst($m->status_mitra ?? '-') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3" style="font-size:.65rem;font-weight:700;background:{{ $m->status_verifikasi=='Verified'?'#dbeafe':'#fef9c3' }};color:{{ $m->status_verifikasi=='Verified'?'#1d4ed8':'#ca8a04' }};">
                                {{ $m->status_verifikasi ?? 'Pending' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <form action="{{ route('admin.monitor.force_logout', $m->id_mitra) }}" method="POST" onsubmit="return confirm('Force logout mitra ini?')" class="d-inline">
                                @csrf
                                <button class="btn-tbl" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-sign-out-alt"></i>Force Logout</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5" style="color:#94a3b8;"><i class="fas fa-broadcast-tower fa-2x mb-2 d-block"></i>Tidak ada data mitra.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ======== TAB: JASTIP ======== --}}
<div id="tab-jastip" style="display:none;">
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="stat-card" style="background:#eff6ff;"><div class="small text-muted fw-bold mb-1">Cuan Zasha</div><div class="fw-bold fs-5" style="color:#005aa9;">Rp {{ number_format($cuan_zasha,0,',','.') }}</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card" style="background:#f0fdf4;"><div class="small text-muted fw-bold mb-1">Order Hari Ini</div><div class="fw-bold fs-5" style="color:#16a34a;">{{ $order_hari_ini }}</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card" style="background:#fef9c3;"><div class="small text-muted fw-bold mb-1">Driver Aktif</div><div class="fw-bold fs-5" style="color:#ca8a04;">{{ $driver_aktif }}</div></div></div>
        <div class="col-6 col-md-3"><div class="stat-card" style="background:#fee2e2;"><div class="small text-muted fw-bold mb-1">Mencari Driver</div><div class="fw-bold fs-5" style="color:#dc2626;">{{ $total_pending }}</div></div></div>
    </div>
    <div class="card-zasha card overflow-hidden">
        <div class="px-4 py-3 border-bottom"><h6 class="fw-bold mb-0" style="color:#1e293b;">Semua Order Jastip</h6></div>
        <div class="table-responsive">
            <table class="table table-adm mb-0">
                <thead><tr>
                    <th class="ps-4">Pelanggan</th><th>Driver</th><th>Status</th>
                    <th class="text-end">Ongkir</th><th class="text-end pe-4">Waktu</th>
                </tr></thead>
                <tbody>
                    @forelse($all_jastip as $j)
                    <tr>
                        <td class="ps-4"><div class="fw-semibold" style="color:#1e293b;">{{ $j->nama_pelanggan }}</div><div class="text-muted" style="font-size:.72rem;">{{ $j->no_wa }}</div></td>
                        <td>{{ $j->nama_driver ?? '<span class="text-muted">Belum ada</span>' }}</td>
                        <td><span class="badge rounded-pill px-2" style="font-size:.65rem;background:#eff6ff;color:#005aa9;">{{ $j->status_jastip }}</span></td>
                        <td class="text-end">Rp {{ number_format($j->ongkir,0,',','.') }}</td>
                        <td class="text-end pe-4 text-muted" style="font-size:.75rem;">{{ \Carbon\Carbon::parse($j->waktu_order)->format('d M, H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-5" style="color:#94a3b8;"><i class="fas fa-motorcycle fa-2x mb-2 d-block"></i>Belum ada order jastip.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<script>
const TABS = ['mitra','kategori','verifikasi','radar','jastip'];

function switchTab(tab, btn) {
    TABS.forEach(t => {
        const el = document.getElementById('tab-' + t);
        if (el) el.style.display = t === tab ? 'block' : 'none';
    });
    document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');
    localStorage.setItem('mitraTab', tab);
}

document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('mitraTab');
    if (saved && TABS.includes(saved)) {
        const idx = TABS.indexOf(saved);
        switchTab(saved, document.querySelectorAll('.tab-pill')[idx]);
    }
});

function addRow(label = '', value = '') {
    const div = document.createElement('div');
    div.className = 'input-group input-group-sm mb-2';
    div.innerHTML = `<input type="text" name="f_label[]" class="form-control" placeholder="Label" value="${label}"><input type="text" name="f_value[]" class="form-control" placeholder="Default" value="${value}"><button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>`;
    document.getElementById('builder-container').appendChild(div);
}

// editKategori & resetForm REMOVED: tab kategori sudah dihilangkan, digantikan halaman /admin/roles
</script>
@endsection
