@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.tarif.index') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Tambah Baru</div>
            <h1 class="m-hero-title">Tarif Layanan</h1>
        </div>
    </div>
</div>

<div class="m-page">
    <div class="m-card">
        <div class="m-card-body m-card-pad-lg">
            <form action="{{ route('mitra.tarif.store') }}" method="POST">
                @csrf
                @include('mitra.tarif._form')
            </form>
        </div>
    </div>
</div>
@endsection
