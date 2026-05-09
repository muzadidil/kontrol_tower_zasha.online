@extends('layouts.admin')

@section('content')
<style>
    .svg-preview { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; }
    .svg-preview svg { width: 22px; height: 22px; }
    .form-label-sm { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 6px; }
    .table-zasha thead th { background: #f8fafc; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 2px solid #e5e7eb; padding: 12px 16px; }
    .table-zasha tbody td { padding: 14px 16px; vertical-align: middle; border-color: #f1f5f9; }
    .table-zasha tbody tr:hover { background: #f8fafc; }
    .badge-status { font-size: 0.65rem; font-weight: 700; padding: 4px 10px; border-radius: 50px; }
    .badge-aktif { background: #dcfce7; color: #16a34a; }
    .badge-segera { background: #fef9c3; color: #ca8a04; }
    .btn-action { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid #e5e7eb; background: white; transition: .15s; }
    .btn-action:hover { background: #f1f5f9; }
    .form-control, .form-select { border-color: #e5e7eb; border-radius: 8px; font-size: 0.875rem; }
    .form-control:focus, .form-select:focus { border-color: #005aa9; box-shadow: 0 0 0 3px rgba(0,90,169,0.08); }
    .svg-textarea { font-family: 'Courier New', monospace; font-size: 0.78rem; resize: vertical; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color:#1e293b;">
            <i class="fas fa-tags me-2" style="color:#005aa9;"></i>Manajemen Kategori
        </h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Kelola jenis layanan jasa dan ikon menu aplikasi.</p>
    </div>
    @if($kategori_edit)
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
            <i class="fas fa-times me-1"></i>Batal Edit
        </a>
    @endif
</div>

@if(session('pesan_notif'))
    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
         style="background:#dcfce7; color:#15803d; font-size:0.875rem;">
        <i class="fas fa-check-circle fs-5"></i>
        <span>{{ session('pesan_notif') }}</span>
    </div>
@endif

<div class="row g-4">

    {{-- FORM TAMBAH / EDIT --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card-zasha card p-4" style="position:sticky; top:1rem;">
            <h6 class="fw-bold mb-4 d-flex align-items-center gap-2" style="color:#1e293b;">
                @if($kategori_edit)
                    <span class="d-flex align-items-center justify-content-center rounded-2"
                          style="width:28px;height:28px;background:#fef3c7;">
                        <i class="fas fa-pencil-alt" style="font-size:0.7rem;color:#d97706;"></i>
                    </span>
                    Edit Kategori
                @else
                    <span class="d-flex align-items-center justify-content-center rounded-2"
                          style="width:28px;height:28px;background:#dbeafe;">
                        <i class="fas fa-plus" style="font-size:0.7rem;color:#005aa9;"></i>
                    </span>
                    Tambah Kategori Baru
                @endif
            </h6>

            <form action="{{ route('admin.kategori.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kategori" value="{{ $kategori_edit->id_kategori ?? '' }}">

                <div class="mb-3">
                    <label class="form-label-sm">Nama Layanan</label>
                    <input type="text" name="nama" class="form-control"
                           value="{{ $kategori_edit->nama_kategori ?? '' }}"
                           placeholder="Contoh: Tukang AC, Cuci Motor..." required>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label-sm">Warna Tema</label>
                        <select name="warna" class="form-select">
                            @foreach(['primary'=>'🔵 Biru','success'=>'🟢 Hijau','danger'=>'🔴 Merah','warning'=>'🟡 Kuning','info'=>'🩵 Cyan','dark'=>'⚫ Hitam'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ ($kategori_edit && $kategori_edit->warna_tema == $val) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label-sm">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif"  {{ ($kategori_edit && $kategori_edit->status == 'aktif')  ? 'selected' : '' }}>✅ Aktif</option>
                            <option value="segera" {{ ($kategori_edit && $kategori_edit->status == 'segera') ? 'selected' : '' }}>🕐 Coming Soon</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label-sm">Kode Ikon SVG</label>
                    <textarea name="svg" class="form-control svg-textarea" rows="5"
                              placeholder='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">...</svg>'
                              required>{{ $kategori_edit->svg_kategori ?? '' }}</textarea>
                    <div class="mt-2 text-muted" style="font-size:0.72rem;">
                        <i class="fas fa-info-circle me-1"></i>Paste kode SVG dari heroicons.com atau iconify.design
                    </div>
                </div>

                <button type="submit" class="btn w-100 fw-bold rounded-pill py-2"
                        style="background:#005aa9; color:white; font-size:0.875rem;">
                    @if($kategori_edit)
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    @else
                        <i class="fas fa-plus me-2"></i>Tambah Kategori
                    @endif
                </button>
            </form>
        </div>
    </div>

    {{-- TABEL LIST KATEGORI --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card-zasha card overflow-hidden">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                <h6 class="fw-bold mb-0" style="color:#1e293b;">
                    Daftar Kategori
                    <span class="badge rounded-pill ms-2" style="background:#eff6ff; color:#005aa9; font-size:0.7rem;">
                        {{ $semua_kategori->count() }} layanan
                    </span>
                </h6>
            </div>

            <div class="table-responsive">
                <table class="table table-zasha mb-0">
                    <thead>
                        <tr>
                            <th style="width:60px;">Ikon</th>
                            <th>Layanan</th>
                            <th class="text-center" style="width:80px;">ID</th>
                            <th class="text-center" style="width:90px;">Status</th>
                            <th class="text-end" style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semua_kategori as $row)
                        <tr>
                            <td>
                                <div class="svg-preview bg-{{ $row->warna_tema ?? 'primary' }} bg-opacity-10 text-{{ $row->warna_tema ?? 'primary' }}">
                                    {!! $row->svg_kategori !!}
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold" style="color:#1e293b; font-size:0.9rem;">{{ $row->nama_kategori }}</div>
                                <div class="text-muted" style="font-size:0.72rem;">Warna: {{ $row->warna_tema ?? '-' }}</div>
                            </td>
                            <td class="text-center">
                                <code style="background:#f1f5f9; color:#475569; padding:3px 8px; border-radius:6px; font-size:0.75rem;">#{{ $row->id_kategori }}</code>
                            </td>
                            <td class="text-center">
                                <span class="badge-status {{ $row->status == 'aktif' ? 'badge-aktif' : 'badge-segera' }}">
                                    {{ $row->status == 'aktif' ? 'Aktif' : 'Coming Soon' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('admin.kategori.index', ['edit' => $row->id_kategori]) }}"
                                       class="btn-action text-primary" title="Edit">
                                        <i class="fas fa-pencil-alt" style="font-size:0.75rem;"></i>
                                    </a>
                                    <form action="{{ route('admin.kategori.destroy', $row->id_kategori) }}" method="POST"
                                          onsubmit="return confirm('Hapus kategori {{ $row->nama_kategori }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action text-danger border-danger" title="Hapus">
                                            <i class="fas fa-trash" style="font-size:0.75rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div style="color:#94a3b8;">
                                    <i class="fas fa-tags fa-2x mb-3 d-block"></i>
                                    <div class="fw-semibold">Belum ada kategori layanan</div>
                                    <div style="font-size:0.8rem;">Tambah kategori pertama menggunakan form di sebelah kiri.</div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
