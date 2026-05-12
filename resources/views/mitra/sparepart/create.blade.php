@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.sparepart.index') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Sparepart Baru</div>
            <h1 class="m-hero-title">Tambah Item</h1>
        </div>
    </div>
</div>

<div class="m-page">
    <div class="m-card">
        <div class="m-card-body m-card-pad-lg">
            <form action="{{ route('mitra.sparepart.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('mitra.sparepart._form')
            </form>
        </div>
    </div>
</div>
@endsection
