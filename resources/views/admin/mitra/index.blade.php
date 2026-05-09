@extends('layouts.admin')

@section('content')
<style>
    .tab-pill { border: none; background: none; padding: 8px 20px; border-radius: 50px; font-weight: 700; font-size: 0.82rem; color: #64748b; cursor: pointer; transition: .2s; }
    .tab-pill.active { background: #005aa9; color: white; }
    .tab-pill:hover:not(.active) { background: #f1f5f9; color: #1e293b; }
    .table-adm thead th { background: #f8fafc; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; padding: 12px 16px; }
    .table-adm tbody td { padding: 13px 16px; vertical-align: middle; border-color: #f1f5f9; font-size: 0.875rem; }
    .table-adm tbody tr:hover { background: #f8fafc; }
    .btn-tbl { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; border: none; cursor: pointer; }
    .form-label-up { font-size: 0.72rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 5px; }
    .form-control, .form-select { border-color: #e5e7eb; border-radius: 8px; font-size: 0.875rem; }
    .form-control:focus, .form-select:focus { border-color: #005aa9; box-shadow: 0 0 0 3px rgba(0,90,169,0.08); }
    .kat-item { border: 1px solid #e5e7eb; border-radius: 14px; padding: 14px; margin-bottom: 10px; background: #fff; }
    .kat-icon-box { width: 46px; height: 46px; border-radius: 12px; background: #eff6ff; color: #005aa9; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
    .badge-field { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-weight: 600; font-size: 0.68rem; padding: 2px 8px; border-radius: 50px; margin: 2px; display: inline-block; }
</style>

{{-- Page Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-users me-2" style="color:#005aa9;"></i>Manajemen Mitra & Kategori
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Kelola mitra penyedia layanan dan kategori pekerjaan.</p>
    </div>
</div>

@if(session('success') || session('pesan'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('success') ?? session('pesan') }}</span>
    </div>
@endif

{{-- Tab Switcher --}}
<div class="mb-4" style="background:#f1f5f9; border-radius:50px; display:inline-flex; padding:4px;">
    <button class="tab-pill active" onclick="switchTab('mitra', this)">
        <i class="fas fa-users me-1"></i>Daftar Mitra
        <span class="badge rounded-pill ms-1" style="background:rgba(255,255,255,0.3); font-size:0.65rem;">{{ $mitras->count() }}</span>
    </button>
    <button class="tab-pill" onclick="switchTab('kategori', this)">
        <i class="fas fa-tags me-1"></i>Kategori Layanan
        <span class="badge rounded-pill ms-1" style="background:rgba(0,90,169,0.1); color:#005aa9; font-size:0.65rem;">{{ $categories->count() }}</span>
    </button>
</div>

{{-- ===== TAB MITRA ===== --}}
<div id="tab-mitra">
    <div class="card-zasha card overflow-hidden">
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
            <h6 class="fw-bold mb-0" style="color:#1e293b;">Daftar Mitra Terdaftar</h6>
            <a href="{{ route('mitra.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold btn-sm">
                <i class="fas fa-plus me-1"></i>Tambah Mitra
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-adm mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Nama Mitra</th>
                        <th>No. WA</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Verifikasi</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mitras as $m)
                    <tr>
                        <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-semibold" style="color:#1e293b;">{{ $m->nama_panggilan }}</div>
                            @if($m->nama_asli)
                                <div class="text-muted" style="font-size:0.75rem;">{{ $m->nama_asli }}</div>
                            @endif
                        </td>
                        <td class="text-muted">{{ $m->no_wa ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3"
                                style="font-size:0.65rem; font-weight:700;
                                       background:{{ $m->status_mitra == 'aktif' ? '#dcfce7' : '#fee2e2' }};
                                       color:{{ $m->status_mitra == 'aktif' ? '#16a34a' : '#dc2626' }};">
                                {{ ucfirst($m->status_mitra ?? 'non-aktif') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3"
                                style="font-size:0.65rem; font-weight:700;
                                       background:{{ $m->status_verifikasi == 'Verified' ? '#dbeafe' : '#fef9c3' }};
                                       color:{{ $m->status_verifikasi == 'Verified' ? '#1d4ed8' : '#ca8a04' }};">
                                {{ $m->status_verifikasi ?? 'Pending' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('mitra.edit', $m->id_mitra) }}" class="btn-tbl" style="background:#eff6ff; color:#005aa9;">
                                    <i class="fas fa-pencil-alt"></i>Edit
                                </a>
                                <form action="{{ route('mitra.destroy', $m->id_mitra) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus mitra {{ $m->nama_panggilan }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-tbl" style="background:#fee2e2; color:#dc2626;">
                                        <i class="fas fa-trash"></i>Hapus
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
                            <div style="font-size:0.8rem;">Klik tombol Tambah Mitra untuk memulai.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== TAB KATEGORI ===== --}}
<div id="tab-kategori" style="display:none;">
    <div class="row g-4">

        {{-- Form --}}
        <div class="col-lg-5">
            <div class="card-zasha card p-4" style="position:sticky; top:1rem;">
                <h6 class="fw-bold mb-4" style="color:#1e293b;" id="form-kat-title">
                    <i class="fas fa-plus me-2" style="color:#005aa9;"></i>Tambah Kategori Baru
                </h6>
                <form action="{{ route('admin.master.kategori.store') }}" method="POST" id="main-form">
                    @csrf
                    <input type="hidden" name="id_edit" id="id_edit">
                    <div class="mb-3">
                        <label class="form-label-up">Nama Layanan</label>
                        <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" placeholder="Contoh: Tukang AC..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-up d-flex justify-content-between align-items-center">
                            Satuan Tarif
                            <a href="#" class="text-primary fw-bold" style="font-size:0.72rem;" data-bs-toggle="modal" data-bs-target="#modalSatuan">+ Kelola Satuan</a>
                        </label>
                        <select name="satuan" id="satuan" class="form-select" required>
                            @foreach($units as $s)
                                <option value="{{ $s->nama_satuan }}">{{ $s->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-up">Ikon (Bootstrap Icon / SVG)</label>
                        <input type="text" name="svg_kategori" id="svg_kategori" class="form-control" placeholder='<i class="bi bi-bag"></i>' required>
                    </div>
                    <div class="mb-4 p-3 rounded-3 border" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="form-label-up mb-0">Tarif Dinamis (Opsional)</span>
                            <button type="button" class="btn btn-sm btn-primary rounded-circle" onclick="addRow()" style="width:26px;height:26px;padding:0;">
                                <i class="fas fa-plus" style="font-size:0.65rem;"></i>
                            </button>
                        </div>
                        <div id="builder-container"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold flex-grow-1 rounded-pill" style="font-size:0.875rem;">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                        <button type="button" onclick="resetForm()" class="btn rounded-pill px-4 fw-bold" style="background:#f1f5f9; color:#64748b; font-size:0.875rem;">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- List Kategori --}}
        <div class="col-lg-7">
            <h6 class="fw-bold mb-3 small text-muted text-uppercase">Kategori Tersedia ({{ $categories->count() }})</h6>
            @forelse($categories as $row)
            <div class="kat-item d-flex align-items-center gap-3">
                <div class="kat-icon-box">{!! $row->svg_kategori !!}</div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="color:#1e293b;">{{ $row->nama_kategori }}</div>
                    <span class="badge rounded-pill px-2 mt-1" style="background:#eff6ff; color:#005aa9; font-size:0.65rem;">Per {{ $row->satuan }}</span>
                    <div class="mt-1">
                        @php $sk = json_decode($row->skema_tarif, true); @endphp
                        @if($sk)
                            @foreach($sk as $s)<span class="badge-field">{{ $s['label'] }}</span>@endforeach
                        @endif
                    </div>
                </div>
                <div class="d-flex gap-1">
                    <button onclick='editKategori(@json($row))' class="btn-tbl" style="background:#eff6ff; color:#005aa9;">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <form action="{{ route('admin.master.kategori.destroy', $row->id_kategori) }}" method="POST"
                          onsubmit="return confirm('Hapus kategori {{ $row->nama_kategori }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-tbl" style="background:#fee2e2; color:#dc2626;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-5" style="color:#94a3b8;">
                <i class="fas fa-tags fa-2x mb-3 d-block"></i>
                <div class="fw-semibold">Belum ada kategori</div>
                <div style="font-size:0.8rem;">Tambah menggunakan form di sebelah kiri.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Satuan --}}
<div class="modal fade" id="modalSatuan" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;">
            <div class="modal-body p-4">
                <h6 class="fw-bold mb-3">Master Satuan</h6>
                <form action="{{ route('admin.master.kategori.unit.store') }}" method="POST" class="mb-3">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="nama_satuan" class="form-control rounded-start-pill" placeholder="Jam, Trip, Unit..." required>
                        <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="fas fa-plus"></i></button>
                    </div>
                </form>
                @foreach($units as $s)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small fw-bold">{{ $s->nama_satuan }}</span>
                    <form action="{{ route('admin.master.kategori.unit.destroy', $s->id_satuan) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="border-0 bg-transparent text-danger"><i class="fas fa-times-circle"></i></button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab, btn) {
    document.getElementById('tab-mitra').style.display    = tab === 'mitra'    ? 'block' : 'none';
    document.getElementById('tab-kategori').style.display = tab === 'kategori' ? 'block' : 'none';
    document.querySelectorAll('.tab-pill').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    localStorage.setItem('mitraTab', tab);
}

// Restore tab state setelah redirect
document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('mitraTab');
    if (saved === 'kategori') {
        switchTab('kategori', document.querySelectorAll('.tab-pill')[1]);
    }
});

function addRow(label = '', value = '') {
    const div = document.createElement('div');
    div.className = 'input-group input-group-sm mb-2';
    div.innerHTML = `
        <input type="text"   name="f_label[]" class="form-control" placeholder="Label (Misal: Berat)" value="${label}">
        <input type="text"   name="f_value[]" class="form-control" placeholder="Default" value="${value}">
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>`;
    document.getElementById('builder-container').appendChild(div);
}

function editKategori(data) {
    document.getElementById('form-kat-title').innerHTML = '<i class="fas fa-pencil-alt me-2" style="color:#d97706;"></i>Edit Kategori';
    document.getElementById('id_edit').value       = data.id_kategori;
    document.getElementById('nama_kategori').value = data.nama_kategori;
    document.getElementById('satuan').value        = data.satuan;
    document.getElementById('svg_kategori').value  = data.svg_kategori;
    document.getElementById('builder-container').innerHTML = '';
    if (data.skema_tarif) {
        JSON.parse(data.skema_tarif).forEach(s => addRow(s.label, s.default));
    }
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById('form-kat-title').innerHTML = '<i class="fas fa-plus me-2" style="color:#005aa9;"></i>Tambah Kategori Baru';
    document.getElementById('id_edit').value = '';
    document.getElementById('main-form').reset();
    document.getElementById('builder-container').innerHTML = '';
}
</script>
@endsection
