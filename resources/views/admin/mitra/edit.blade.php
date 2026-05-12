@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Edit Mitra: {{ $mitra->nama_panggilan ?? $mitra->name }}</h1>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Role & Akses Card --}}
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-shield-alt me-2"></i> Role & Akses Fitur
            </div>
            <div class="card-body">
                <form action="{{ route('admin.mitra.assignRole', $mitra->id_mitra) }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Role Mitra</label>
                            <select name="role_id" class="form-select rounded-3">
                                <option value="">-- Tanpa Role (tidak bisa akses fitur apapun) --</option>
                                @foreach(($roles ?? []) as $role)
                                    @if($role->is_active || $mitra->role_id == $role->id)
                                        <option value="{{ $role->id }}" {{ $mitra->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }} @if(!$role->is_active) [DRAFT] @endif @if($role->description) — {{ $role->description }} @endif
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            <div class="form-text">Hanya role yang sudah dirilis (aktif) yang bisa di-assign.</div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold w-100">
                                <i class="bi bi-check-lg me-1"></i> Simpan Role
                            </button>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-primary rounded-pill px-4 w-100">
                                <i class="bi bi-gear me-1"></i> Kelola Role
                            </a>
                        </div>
                    </div>
                    @if($mitra->role_id && $mitra->role)
                        <div class="mt-3 p-3 bg-light rounded-3">
                            <div class="text-muted small fw-bold mb-2">FITUR YANG BISA DIAKSES SAAT INI:</div>
                            @forelse($mitra->role->features as $f)
                                <span class="badge bg-success me-1 mb-1" style="padding: 6px 12px; font-size: 11px;">
                                    {{ \App\Models\Role::ALL_FEATURES[$f->feature_key] ?? $f->feature_key }}
                                </span>
                            @empty
                                <span class="text-muted fst-italic">Role ini tidak punya fitur. Mitra tidak akan bisa akses apapun.</span>
                            @endforelse
                        </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tarif Mitra Card --}}
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-cash-coin me-2"></i> Tarif & Komisi Mitra
                </div>
                <span class="badge bg-light text-success fw-bold">
                    Komisi Zasha: {{ number_format($komisiPersen ?? 10, 1) }}%
                </span>
            </div>
            <div class="card-body p-0">
                @if($mitra->tarifs->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        <div class="fst-italic">Mitra belum punya daftar tarif.</div>
                        <small>Tarif akan muncul setelah mitra menambahkannya dari dashboard.</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Keterangan</th>
                                    <th class="text-end">Tarif Mitra</th>
                                    <th class="text-end">Keuntungan Admin</th>
                                    <th class="text-end pe-3">Harga Pelanggan</th>
                                    <th class="text-center" style="width:80px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mitra->tarifs as $t)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="fw-semibold">{{ $t->keterangan }}</div>
                                            <small class="text-muted">per {{ $t->satuan ?? 'unit' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-semibold">Rp {{ number_format($t->nominal, 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-end text-warning">
                                            +Rp {{ number_format($t->keuntunganAdmin(), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-bold text-success">
                                                Rp {{ number_format($t->hargaPelanggan(), 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($t->is_aktif)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Non-aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="ps-3 fw-bold">Total Aktif: {{ $mitra->tarifs->where('is_aktif', true)->count() }} tarif</td>
                                    <td class="text-end fw-bold">
                                        Rp {{ number_format($mitra->tarifs->where('is_aktif', true)->sum('nominal'), 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-bold text-warning">
                                        +Rp {{ number_format($mitra->tarifs->where('is_aktif', true)->sum(fn($t) => $t->keuntunganAdmin()), 0, ',', '.') }}
                                    </td>
                                    <td class="text-end pe-3 fw-bold text-success">
                                        Rp {{ number_format($mitra->tarifs->where('is_aktif', true)->sum(fn($t) => $t->hargaPelanggan()), 0, ',', '.') }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.mitra.update', $mitra->id_mitra) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama_panggilan" class="form-control" value="{{ $mitra->nama_panggilan }}" required>
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
