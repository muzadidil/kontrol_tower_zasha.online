@extends('layouts.pelanggan')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/pelanggan/alamat.css') }}">
@endsection

@section('content')
<div class="container my-4 animate-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0">Buku Alamat</h5>
        <button class="btn btn-primary rounded-pill btn-sm px-3" onclick="bukaModalTambah()">+ Tambah Alamat</button>
    </div>

    <div class="row g-3">
        @forelse($alamats as $a)
        <div class="col-md-6">
            <div class="card card-zasha p-3 {{ $a->is_utama ? 'border-primary shadow-sm' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-light text-dark mb-2">{{ $a->label_alamat }}</span>
                        <h6 class="fw-bold mb-1">{{ $a->nama_penerima }}</h6>
                        <p class="small text-muted mb-1">{{ $a->no_wa_penerima }}</p>
                        <p class="small mb-0">{{ $a->alamat_lengkap }}</p>
                    </div>
                    @if($a->is_utama)
                        <span class="badge bg-primary">Utama</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">Belum ada alamat tersimpan.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/pelanggan/alamat.js') }}"></script>
<script src="{{ asset('assets/js/maps.js') }}"></script>
@endsection
