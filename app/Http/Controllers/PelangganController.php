<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        // Data sementara untuk memastikan dashboard tampil
        $nama_tampil = "muzadidil";
        $foto_user = "https://ui-avatars.com/api/?name=Ricky+Akbar&background=002d72&color=fff";

        // Mengarahkan ke resources/views/pelanggan/dashboard.blade.php
        return view('pelanggan.dashboard', compact('nama_tampil', 'foto_user'));
    }
}