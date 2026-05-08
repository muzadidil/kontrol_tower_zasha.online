<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        // 1. Buat Dummy Data User
        // Kita gunakan stdClass untuk meniru format object dari database/auth
        $user = new \stdClass();
        $user->name = 'Muzadidil Akbar'; 
        $user->foto = ''; // Kosongkan agar fitur ui-avatars berjalan
        $user->kode_zasha = 'ZSH-001';
        $user->is_verif = 1; // Ganti ke 1 nanti untuk mengetes menu yang terbuka
        $user->saldo = 150000;

        // 2. Buat Dummy Data Kategori Layanan
        $cat1 = new \stdClass();
        $cat1->id_kategori = 1;
        $cat1->nama_kategori = 'Jastip Pasar';
        $cat1->svg_kategori = '<i class="bi bi-bag-check-fill fs-3 text-primary"></i>';

        $cat2 = new \stdClass();
        $cat2->id_kategori = 2;
        $cat2->nama_kategori = 'Servis AC';
        $cat2->svg_kategori = '<i class="bi bi-tools fs-3 text-info"></i>';

        $cat3 = new \stdClass();
        $cat3->id_kategori = 3;
        $cat3->nama_kategori = 'Topup Game';
        $cat3->svg_kategori = '<i class="bi bi-controller fs-3 text-success"></i>';

        $cat4 = new \stdClass();
        $cat4->id_kategori = 4;
        $cat4->nama_kategori = 'Kirim Barang';
        $cat4->svg_kategori = '<i class="bi bi-box-seam-fill fs-3 text-warning"></i>';

        // Masukkan semua kategori ke dalam array
        $categories = [$cat1, $cat2, $cat3, $cat4];

        // 3. Lempar data dummy ke view
        return view('pelanggan.dashboard', compact('user', 'categories'));
    }
}