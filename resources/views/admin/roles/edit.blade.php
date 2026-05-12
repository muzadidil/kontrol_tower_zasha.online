@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold m-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Role: {{ $role->name }}</h4>
        <small class="text-muted">Ubah fitur yang bisa diakses role ini.</small>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light rounded-pill px-4">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.roles._form')
        </form>
    </div>
</div>
@endsection
