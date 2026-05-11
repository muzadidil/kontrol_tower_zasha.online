@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Edit Mitra</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.mitra.update', $mitra->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ $mitra->nama }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $mitra->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control" value="{{ $mitra->kategori }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Saldo</label>
                        <input type="text" class="form-control" value="{{ $mitra->saldo }}" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Is WFH</label>
                        <select name="is_wfh" class="form-select">
                            <option value="1" {{ $mitra->is_wfh ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ !$mitra->is_wfh ? 'selected' : '' }}>Tidak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarif per KM</label>
                        <input type="number" name="tarif_per_km" class="form-control" value="{{ $mitra->tarif_per_km }}" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Service Standar</label>
                        <input type="number" name="biaya_service_standar" class="form-control" value="{{ $mitra->biaya_service_standar }}" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.mitra.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
