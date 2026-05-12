@extends('layouts.mitra')

@section('content')
@include('mitra.partials._page-style')

<div class="m-hero">
    <div class="m-hero-bar">
        <a href="{{ route('mitra.tarif.index') }}" class="m-hero-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="m-hero-eyebrow">Edit Tarif</div>
            <h1 class="m-hero-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $tarif->keterangan }}</h1>
        </div>
    </div>
</div>

<div class="m-page">
    <div class="m-card">
        <div class="m-card-body m-card-pad-lg">
            <form action="{{ route('mitra.tarif.update', $tarif->id) }}" method="POST">
                @csrf @method('PUT')
                @include('mitra.tarif._form')
            </form>
        </div>
    </div>
</div>
@endsection
