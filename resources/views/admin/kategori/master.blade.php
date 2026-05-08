@extends('layouts.admin')

@section('content')
<style>
    .mobile-wrapper { max-width: 800px; margin: 0 auto; padding-bottom: 80px; }
    .card-zasha { border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); background: white; }
    .label-kat { font-size: 11px; font-weight: 800; color: #a3aed0; letter-spacing: 1px; }
    .kat-item { border: 1px solid #f0f2f5; border-radius: 16px; padding: 15px; margin-bottom: 12px; background: #fff; }
    .kat-icon-box { width: 50px; height: 50px; border-radius: 14px; background: #eef2ff; color: #002d72; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
    .badge-field { background: #f4f7fe; color: #2b3674; border: 1px solid #e2e8f0; font-weight: 600; font-size: 10px; margin-right: 4px; margin-bottom: 4px; display: inline-block;}
</style>

<div id="master-kategori-wrapper" class="mobile-wrapper animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h5 class="fw-bold m-0"><i class="bi bi-grid-fill text-primary me-2"></i>Kategori Layanan</h5>
            <small class="text-muted">Atur layanan jastip & tarif.</small>
        </div>
        <a href="{{ route('admin.kategori.index') }}" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm border fw-bold text-dark">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card card-zasha p-4 shadow-sm">
                <h6 class="fw-bold mb-4 text-primary border-bottom pb-2" id="form-title">➕ Tambah Kategori Baru</h6>
                
                <form action="{{ route('admin.master.kategori.store') }}" method="POST" id="main-form">
                    @csrf
                    <input type="hidden" name="id_edit" id="id_edit">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label label-kat">NAMA PEKERJAAN / LAYANAN</label>
                            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control rounded-3" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label label-kat d-flex justify-content-between">
                                SATUAN TARIF UTAMA
                                <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#modalSatuan">+ Kelola</a>
                            </label>
                            <select name="satuan" id="satuan" class="form-select rounded-3" required>
                                @foreach($units as $s)
                                    <option value="{{ $s->nama_satuan }}">{{ $s->nama_satuan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label label-kat">ICON LAYANAN (KODE BOOTSTRAP)</label>
                            <input type="text" name="svg_kategori" id="svg_kategori" class="form-control rounded-3" required placeholder='<i class="bi bi-bag"></i>'>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0" style="font-size: 13px;">Custom Fields (Tarif Dinamis)</h6>
                            <button type="button" class="btn btn-primary btn-sm rounded-circle" onclick="addRow()"><i class="bi bi-plus-lg"></i></button>
                        </div>
                        <div id="builder-container" class="mb-2"></div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary fw-bold flex-grow-1 rounded-pill">SIMPAN DATA</button>
                        <button type="button" onclick="resetForm()" class="btn btn-light border fw-bold rounded-pill px-4">Reset</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 mt-4">
            <h6 class="fw-bold mb-3 small text-muted text-uppercase">Daftar Kategori Tersedia</h6>
            @foreach($categories as $row)
            <div class="kat-item shadow-sm d-flex align-items-center">
                <div class="kat-icon-box shadow-sm">{!! $row->svg_kategori !!}</div>
                <div class="flex-grow-1 px-3">
                    <h6 class="fw-bold mb-1 text-dark">{{ $row->nama_kategori }}</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2" style="font-size: 10px;">Per {{ $row->satuan }}</span>
                    <div class="d-block">
                        @php $sk = json_decode($row->skema_tarif, true); @endphp
                        @if($sk)
                            @foreach($sk as $s) <span class="badge badge-field rounded-pill">{{ $s['label'] }}</span> @endforeach
                        @endif
                    </div>
                </div>
                <div class="d-flex flex-column gap-2">
                    <button onclick='editKategori(@json($row))' class="btn btn-sm btn-light border text-primary rounded-circle"><i class="bi bi-pencil-fill"></i></button>
                    <form action="{{ route('admin.master.kategori.destroy', $row->id_kategori) }}" method="POST" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle"><i class="bi bi-trash3-fill"></i></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- MODAL SATUAN --}}
<div class="modal fade" id="modalSatuan" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4">
                <h6 class="fw-bold mb-3">Master Satuan</h6>
                <form action="{{ route('admin.master.kategori.unit.store') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="nama_satuan" class="form-control rounded-start-pill" placeholder="Kg/Trip" required>
                        <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </form>
                @foreach($units as $s)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="small fw-bold">{{ $s->nama_satuan }}</span>
                    <form action="{{ route('admin.master.kategori.unit.destroy', $s->id_satuan) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-danger border-0 bg-transparent"><i class="bi bi-x-circle-fill"></i></button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function addRow(label = '', value = '') {
        const container = document.getElementById('builder-container');
        const div = document.createElement('div');
        div.className = 'input-group input-group-sm mb-2';
        div.innerHTML = `
            <input type="text" name="f_label[]" class="form-control" placeholder="Label (Misal: Berat)" value="${label}">
            <input type="text" name="f_value[]" class="form-control" placeholder="Default" value="${value}">
            <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()"><i class="bi bi-trash"></i></button>
        `;
        container.appendChild(div);
    }

    function editKategori(data) {
        document.getElementById('form-title').innerHTML = '📝 Edit Kategori';
        document.getElementById('id_edit').value = data.id_kategori;
        document.getElementById('nama_kategori').value = data.nama_kategori;
        document.getElementById('satuan').value = data.satuan;
        document.getElementById('svg_kategori').value = data.svg_kategori;
        
        document.getElementById('builder-container').innerHTML = '';
        if(data.skema_tarif) {
            let skema = JSON.parse(data.skema_tarif);
            skema.forEach(s => addRow(s.label, s.default));
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('form-title').innerHTML = '➕ Tambah Kategori Baru';
        document.getElementById('id_edit').value = '';
        document.getElementById('main-form').reset();
        document.getElementById('builder-container').innerHTML = '';
    }
</script>
@endsection
