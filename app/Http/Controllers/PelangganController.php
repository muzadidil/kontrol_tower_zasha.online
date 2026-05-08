<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index()
    {
        // Dummy Data User untuk Dashboard
        $user = new \stdClass();
        $user->name = 'Muzadidil Akbar'; 
        $user->foto = ''; 
        $user->kode_zasha = 'ZSH-001';
        $user->is_verif = 1; 
        $user->saldo = 150000;

        // Dummy Data Kategori
        $cat1 = new \stdClass(); $cat1->id_kategori = 1; $cat1->nama_kategori = 'Jastip Pasar'; $cat1->svg_kategori = '<i class="bi bi-bag-check-fill fs-3 text-primary"></i>';
        $cat2 = new \stdClass(); $cat2->id_kategori = 2; $cat2->nama_kategori = 'Servis AC'; $cat2->svg_kategori = '<i class="bi bi-tools fs-3 text-info"></i>';

        $categories = [$cat1, $cat2];

        return view('pelanggan.dashboard', compact('user', 'categories'));
    }

    public function alamat()
    {
        $id_cust = Auth::id() ?? session('id_pelanggan'); 
        $alamat = DB::table('alamat')->where('id_pelanggan', $id_cust)->orderBy('is_utama', 'desc')->get();
        return view('alamat', compact('alamat'));
    }

    public function dompet()
    {
        $id_p = Auth::id() ?? session('id_pelanggan');
        $pelanggan = DB::table('pelanggan')->where('id_pelanggan', $id_p)->first();
        $riwayat = DB::table('dompet_pelanggan')->where('id_pelanggan', $id_p)->orderBy('waktu_request', 'desc')->get();
        return view('dompet', compact('pelanggan', 'riwayat'));
    }

    public function invoice($id)
    {
        $d = DB::table('pesanan as p')
            ->join('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->join('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->select('p.*', 'm.nama_mitra', 'pl.nama_pelanggan')
            ->where('p.id_pesanan', $id)
            ->first();
        return view('invoice', compact('d'));
    }

    public function simpanPesanan(Request $request)
    {
        $id_pelanggan = Auth::id() ?? session('id_pelanggan');
        $mitra = DB::table('mitra')->where('id_mitra', $request->id_mitra)->first();
        
        $id_pesanan = DB::table('pesanan')->insertGetId([
            'id_pelanggan' => $id_pelanggan,
            'id_mitra' => $request->id_mitra,
            'id_kategori' => $mitra->id_kategori,
            'tanggal_pesanan' => now(),
            'total_pesanan' => ($mitra->tarif_per_jam * $request->durasi) + 5000 + rand(111,999),
            'status_pesanan' => 'Pending'
        ]);

        return redirect()->route('pelanggan.invoice', $id_pesanan);
    }
}