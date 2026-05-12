@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.portfolio.index') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Portfolio Baru</div>
            <h1 class="m-hero-title">Tambah Karya</h1>
        </div>
    </div>
</div>

<div class="m-page">
    <div class="m-card">
        <div class="m-card-body m-card-pad-lg">
            <form action="{{ route('mitra.portfolio.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('mitra.portfolio._form')
            </form>
        </div>
    </div>
</div>
@endsection
