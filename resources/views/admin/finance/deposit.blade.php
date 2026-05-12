@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="fas fa-plus-circle text-primary me-2"></i> Deposit Manual
            </h4>
            <small class="text-muted">Tambah saldo secara manual ke akun Pelanggan atau Mitra</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-xl-6 col-lg-8 col-md-10">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4 py-3 px-4">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-wallet me-2"></i> Form Deposit Saldo
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.finance.storeDeposit') }}" method="POST">
                        @csrf

                        {{-- Tipe User --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                Tipe User <span class="text-danger">*</span>
                            </label>
                            <select name="tipe_user" class="form-select @error('tipe_user') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Tipe --</option>
                                <option value="Pelanggan" {{ old('tipe_user') == 'Pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                                <option value="Mitra" {{ old('tipe_user') == 'Mitra' ? 'selected' : '' }}>Mitra</option>
                            </select>
                            @error('tipe_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- User ID --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                User ID <span class="text-danger">*</span>
                            </label>
                            <input
                                type="number"
                                name="user_id"
                                class="form-control @error('user_id') is-invalid @enderror"
                                placeholder="Masukkan ID user"
                                value="{{ old('user_id') }}"
                                min="1"
                                required
                            >
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jumlah Nominal --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                Jumlah Nominal <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold text-muted">Rp</span>
                                <input
                                    type="number"
                                    name="jumlah_nominal"
                                    class="form-control @error('jumlah_nominal') is-invalid @enderror"
                                    placeholder="Contoh: 50000"
                                    value="{{ old('jumlah_nominal') }}"
                                    min="1000"
                                    required
                                >
                                @error('jumlah_nominal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">Keterangan</label>
                            <input
                                type="text"
                                name="keterangan"
                                class="form-control"
                                placeholder="Opsional: alasan deposit manual"
                                value="{{ old('keterangan') }}"
                            >
                        </div>

                        {{-- Submit --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary fw-bold py-2 rounded-3">
                                <i class="fas fa-paper-plane me-2"></i> Simpan Deposit
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection