@extends('layouts.admin')
@section('title', 'Import Produk Game')

@section('content')
<div class="container-fluid py-4" style="max-width: 760px;">
    <a href="{{ route('admin.game-topup.index') }}" class="btn btn-link text-warning ps-0 mb-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    <h4 class="fw-bold mb-1"><i class="fas fa-cloud-download-alt text-warning me-2"></i>Import Produk dari Digiflazz</h4>
    <p class="text-muted small mb-4">Fetch pricelist Digiflazz, filter by brand, dan simpan ke kategori produk.</p>

    @foreach(['success','error','warning'] as $t)
        @if(session($t)) <div class="alert alert-{{$t}} alert-dismissible fade show">{{ session($t) }}<button class="btn-close" data-bs-dismiss="alert"></button></div> @endif
    @endforeach

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="alert alert-info small d-flex align-items-start gap-2 mb-4">
                <i class="fas fa-info-circle mt-1"></i>
                <div>
                    <strong>Cara Kerja:</strong>
                    <ol class="mb-0 ps-3">
                        <li>Sistem fetch semua produk dari Digiflazz</li>
                        <li>Filter berdasarkan <strong>Brand</strong> yang Anda input (case-sensitive!)</li>
                        <li>Buat/update kategori, lalu simpan semua nominal sebagai produk</li>
                        <li>Harga jual = harga modal × (1 + profit%)</li>
                    </ol>
                </div>
            </div>

            <form action="{{ route('admin.game-topup.import.store') }}" method="POST">@csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Brand di Digiflazz <span class="text-danger">*</span></label>
                        <input type="text" name="brand" class="form-control" value="{{ old('brand') }}" required placeholder="Mobile Legends">
                        <div class="form-text">Persis seperti di pricelist Digiflazz (case-sensitive). Contoh: <code>Mobile Legends</code>, <code>Free Fire</code>, <code>PUBG Mobile</code>.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Kode Kategori (URL slug) <span class="text-danger">*</span></label>
                        <input type="text" name="kode_kategori" class="form-control" value="{{ old('kode_kategori') }}" required placeholder="mobile-legends">
                        <div class="form-text">Untuk URL: <code>/game/{{ old('kode_kategori', 'kode-kategori') }}</code></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Sub Nama (opsional)</label>
                        <input type="text" name="sub_nama" class="form-control" value="{{ old('sub_nama') }}" placeholder="Diamond / UC / etc">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">URL Thumbnail</label>
                        <input type="text" name="thumbnail" class="form-control" value="{{ old('thumbnail') }}" placeholder="https://...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Tipe <span class="text-danger">*</span></label>
                        <select name="tipe" class="form-select" required>
                            <option value="game" {{ old('tipe')==='game'?'selected':'' }}>Game</option>
                            <option value="voucher" {{ old('tipe')==='voucher'?'selected':'' }}>Voucher</option>
                            <option value="pulsa" {{ old('tipe')==='pulsa'?'selected':'' }}>Pulsa</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Butuh Server/Zone ID? <span class="text-danger">*</span></label>
                        <select name="server_id" class="form-select" required>
                            <option value="0" {{ old('server_id')==='0'?'selected':'' }}>Tidak (UID saja)</option>
                            <option value="1" {{ old('server_id')==='1'?'selected':'' }}>Ya (UID + Zone)</option>
                        </select>
                        <div class="form-text">ML butuh Zone, FF tidak.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Profit (%) <span class="text-danger">*</span></label>
                        <input type="number" name="profit" class="form-control" value="{{ old('profit', 5) }}" min="0" max="100" required>
                        <div class="form-text">Markup dari harga modal Digiflazz.</div>
                    </div>
                </div>

                <hr class="my-4">
                <button type="submit" class="btn btn-warning fw-bold px-4">
                    <i class="fas fa-cloud-download-alt me-1"></i> Mulai Import
                </button>
                <a href="{{ route('admin.game-topup.index') }}" class="btn btn-outline-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection
