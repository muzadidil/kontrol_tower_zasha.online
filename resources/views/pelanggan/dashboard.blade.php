@extends('layouts.app')

@section('content')
<style>
    :root { --zasha-blue: #002d72; }
    .hero-section { background: var(--zasha-blue); color: white; padding: 40px 20px 60px; border-radius: 0 0 30px 30px; }
    .menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-top: -30px; padding: 0 15px; }
    .menu-item { background: white; border-radius: 20px; padding: 15px 5px; text-align: center; text-decoration: none; color: #333; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.2s; }
    .menu-item:active { transform: scale(0.9); }
    .menu-icon { font-size: 24px; color: var(--zasha-blue); margin-bottom: 5px; display: block; }
    .menu-label { font-size: 10px; font-weight: 700; text-transform: uppercase; }
</style>

<div class="hero-section animate-in">
    <h5 class="fw-bold mb-1">Halo, Selamat Datang!</h5>
    <p class="small opacity-75">Mau pesan jasa apa hari ini di Zasha?</p>
</div>

<div class="menu-grid animate-in">
    {{-- Contoh Item Menu --}}
    <a href="{{ route('pelanggan.dompet') }}" class="menu-item">
        <i class="bi bi-wallet2 menu-icon"></i>
        <span class="menu-label">Dompet</span>
    </a>
    <a href="{{ route('pelanggan.alamat') }}" class="menu-item">
        <i class="bi bi-geo-alt-fill menu-icon"></i>
        <span class="menu-label">Alamat</span>
    </a>
    <a href="{{ route('pelanggan.riwayat.index') }}" class="menu-item">
        <i class="bi bi-clock-history menu-icon"></i>
        <span class="menu-label">Pesanan</span>
    </a>
    <a href="{{ route('pelanggan.profil') }}" class="menu-item">
        <i class="bi bi-person-fill menu-icon"></i>
        <span class="menu-label">Profil</span>
    </a>
</div>

<div class="container mt-4 animate-in">
    <h6 class="fw-bold mb-3">Layanan Kami</h6>
    <div class="row g-3">
        {{-- Di sini nanti Bapak bisa looping kategori dari database --}}
        <div class="col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 text-center">
                <i class="bi bi-bag-check-fill fs-1 text-primary mb-2"></i>
                <h6 class="fw-bold mb-0">Jastip Pasar</h6>
            </div>
        </div>
        <div class="col-6">
            <div class="card border-0 rounded-4 shadow-sm p-3 text-center">
                <i class="bi bi-tools fs-1 text-primary mb-2"></i>
                <h6 class="fw-bold mb-0">Servis AC</h6>
            </div>
        </div>
    </div>
</div>
@endsection
