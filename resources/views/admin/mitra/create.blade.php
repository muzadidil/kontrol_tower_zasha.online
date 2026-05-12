@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Tambah Mitra</h1>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.mitra.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="kategori" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nomor WA</label>
                        <input type="text" name="nomor_wa" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role Mitra</label>
                        <select name="role_id" class="form-select">
                            <option value="">-- Tanpa Role (assign nanti) --</option>
                            @foreach(\App\Models\Role::where('is_active', true)->orderBy('name')->get() as $role)
                                <option value="{{ $role->id }}">
                                    {{ $role->name }} @if($role->description) — {{ $role->description }} @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Role menentukan fitur yang bisa diakses mitra ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Is WFH</label>
                        <select name="is_wfh" class="form-select">
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tarif per KM</label>
                        <input type="number" name="tarif_per_km" class="form-control" step="0.01">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biaya Service Standar</label>
                        <input type="number" name="biaya_service_standar" class="form-control" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Mitra</button>
                    <a href="{{ route('admin.mitra.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
@endsection
