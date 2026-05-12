@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Role Baru</h4>
        <small class="text-muted">Buat role kustom dengan kombinasi fitur sesuai kebutuhan.</small>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf
            @include('admin.roles._form')
        </form>
    </div>
</div>
@endsection
