@extends('layouts.pelanggan')

@section('content')
<div class="container mt-4">
    <h3>Profil Pelanggan</h3>
    <form action="{{ route('profil.update') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama_pelanggan" class="form-control" value="{{ $pelanggan->nama_pelanggan }}">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $pelanggan->email }}">
        </div>
        <button type="submit" class="btn btn-primary">Update Profil</button>
    </form>

    <hr>

    <h3>Alamat</h3>
    @foreach($alamat as $a)
        <p>{{ $a->alamat }}</p>
    @endforeach
    
    <form action="{{ route('profil.alamat.store') }}" method="POST">
        @csrf
        <input type="text" name="alamat" placeholder="Alamat baru" class="form-control mb-2">
        <input type="hidden" name="latitude" value="0">
        <input type="hidden" name="longitude" value="0">
        <button type="submit" class="btn btn-success">Tambah Alamat</button>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/pelanggan/profil.js') }}"></script>
@endpush
