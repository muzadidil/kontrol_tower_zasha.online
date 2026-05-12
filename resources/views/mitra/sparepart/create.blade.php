@extends('layouts.mitra')

@section('content')
<div style="background: linear-gradient(135deg, var(--mitra-blue, #005aa9) 0%, var(--mitra-blue-light, #0078d4) 100%); padding: var(--fib-5, 24px) var(--fib-4, 16px) var(--fib-6, 32px); color: #fff;">
    <div class="d-flex align-items-center">
        <a href="{{ route('mitra.sparepart.index') }}" class="text-white me-3" style="font-size:1.4rem;text-decoration:none;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="font-size:.75rem;opacity:.8;">SPAREPART BARU</div>
            <h5 class="fw-bold m-0">Tambah Item</h5>
        </div>
    </div>
</div>

<div style="padding: var(--fib-4, 16px); max-width: 720px; margin: 0 auto;">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('mitra.sparepart.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('mitra.sparepart._form')
            </form>
        </div>
    </div>
</div>
@endsection
