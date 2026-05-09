@extends('layouts.pelanggan')

@section('content')
<style>
    .star-rating { display: flex; flex-direction: row-reverse; justify-content: center; }
    .star-rating input { display: none; }
    .star-rating label { font-size: 2.5rem; color: #ddd; cursor: pointer; transition: 0.2s; }
    .star-rating input:checked ~ label { color: #ffca08; }
    .star-rating label:hover, .star-rating label:hover ~ label { color: #ffca08; }
</style>

<div class="container mt-5 animate-in" style="max-width: 500px;">
    <div class="card border-0 shadow-sm rounded-4 text-center p-4">
        <h5 class="fw-bold">Gimana kerjaan {{ $order->nama_mitra }}?</h5>
        <p class="text-muted small">Rating kamu sangat berarti buat warga Jember lainnya!</p>
        
        <form action="{{ route('pelanggan.review.kirim') }}" method="POST">
            @csrf
            <input type="hidden" name="id_order" value="{{ $order->id_order }}">
            <input type="hidden" name="id_mitra" value="{{ $order->id_mitra }}">

            <div class="star-rating mb-3">
                <input type="radio" id="star5" name="rating" value="5" required/><label for="star5" class="bi bi-star-fill"></label>
                <input type="radio" id="star4" name="rating" value="4"/><label for="star4" class="bi bi-star-fill"></label>
                <input type="radio" id="star3" name="rating" value="3"/><label for="star3" class="bi bi-star-fill"></label>
                <input type="radio" id="star2" name="rating" value="2"/><label for="star2" class="bi bi-star-fill"></label>
                <input type="radio" id="star1" name="rating" value="1"/><label for="star1" class="bi bi-star-fill"></label>
            </div>

            <div class="mb-4">
                <textarea name="komentar" class="form-control" rows="3" placeholder="Tulis komentar kamu di sini..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2 shadow-sm">Kirim Ulasan</button>
            <a href="{{ route('pelanggan.profil') }}" class="btn btn-link text-muted mt-2 text-decoration-none">Nanti Saja</a>
        </form>
    </div>
</div>
@endsection
