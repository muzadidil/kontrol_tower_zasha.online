@extends('layouts.admin')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/admin_kategori.css') }}">
<script src="{{ asset('assets/js/admin_kategori.js') }}" defer></script>

<div id="kategori-wrapper" class="animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold m-0"><i class="bi bi-tags-fill text-primary me-2"></i>Manajemen Kategori</h4>
            <small class="text-muted">Kelola jenis layanan jasa dan ikon menu aplikasi.</small>
        </div>
        <div class="d-flex gap-2">
            @if($kategori_edit)
                <a href="{{ route('admin.kategori.index') }}" class="btn btn-secondary rounded-pill px-3 btn-sm fw-bold">Batal Edit</a>
            @endif
        </div>
    </div>

    @if(session('pesan_notif'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate-in">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('pesan_notif') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-zasha p-4 sticky-form">
                <h6 class="fw-bold mb-3">{{ $kategori_edit ? "📝 Edit Kategori" : "➕ Tambah Kategori Baru" }}</h6>
                <form action="{{ route('admin.kategori.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_kategori" value="{{ $kategori_edit->id_kategori ?? '' }}">
                    
                    <div class="mb-3">
                        <label class="form-label label-custom">Nama Layanan</label>
                        <input type="text" name="nama" class="form-control rounded-3" value="{{ $kategori_edit->nama_kategori ?? '' }}" placeholder="Misal: Tukang AC..." required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label label-custom">Warna Tema</label>
                            <select name="warna" class="form-select rounded-3">
                                @foreach(['primary'=>'Biru','success'=>'Hijau','danger'=>'Merah','warning'=>'Kuning','info'=>'Cyan','dark'=>'Hitam'] as $val => $label)
                                    <option value="{{ $val }}" {{ ($kategori_edit && $kategori_edit->warna_tema == $val) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label label-custom">Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="aktif" {{ ($kategori_edit && $kategori_edit->status == 'aktif') ? 'selected' : '' }}>Aktif</option>
                                <option value="segera" {{ ($kategori_edit && $kategori_edit->status == 'segera') ? 'selected' : '' }}>Coming Soon</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label label-custom">Kode Ikon SVG</label>
                        <textarea name="svg" class="form-control rounded-3 font-monospace input-svg" rows="4" placeholder='<svg ...>' required>{{ $kategori_edit->svg_kategori ?? '' }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                        {{ $kategori_edit ? "UPDATE PERUBAHAN 🚀" : "SIMPAN KATEGORI ➕" }}
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-zasha overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-zasha table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Ikon</th>
                                <th>Kategori</th>
                                <th class="text-center">ID</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($semua_kategori as $row)
                            <tr>
                                <td class="ps-4">
                                    <div class="icon-container bg-{{ $row->warna_tema }} bg-opacity-10 text-{{ $row->warna_tema }}">
                                        <div class="svg-holder">{!! $row->svg_kategori !!}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $row->nama_kategori }}</div>
                                    <span class="badge badge-pill-zasha bg-{{ $row->status == 'aktif' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $row->status == 'aktif' ? 'success' : 'secondary' }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td class="text-center"><code class="id-badge">{{ $row->id_kategori }}</code></td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.kategori.index', ['edit' => $row->id_kategori]) }}" class="btn btn-sm btn-action text-primary">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.kategori.destroy', $row->id_kategori) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-action text-danger border-0 bg-transparent">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted small">Belum ada kategori layanan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
