<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriPekerjaan;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index()
    {
        // Mengambil data user yang login, jika tidak ada gunakan data dummy agar tidak error undefined variable
        $user = Auth::user() ?? (object) [
            'name' => 'muzadidil',
            'foto' => 'https://ui-avatars.com/api/?name=Ricky+Akbar&background=002d72&color=fff',
            'kode_zasha' => 'ZSH-001',
            'saldo' => 0,
            'is_verif' => 1
        ];

        // Mengambil data kategori dari database, gunakan try-catch agar tidak crash jika tabel belum ada
        try {
            $categories = KategoriPekerjaan::orderBy('id_kategori', 'asc')->get();
        } catch (\Exception $e) {
            $categories = collect([]);
        }

        // Mengarahkan ke resources/views/pelanggan/dashboard.blade.php
        return view('pelanggan.dashboard', compact('user', 'categories'));
    }
}