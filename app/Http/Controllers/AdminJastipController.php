<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminJastipController extends Controller
{
    public function index()
    {
        // 1. Rumus Cuan Zasha (TIDAK DIUBAH)
        $cuan_zasha = DB::table('pesanan_jastip')
            ->where('status_jastip', 'Selesai')
            ->sum(DB::raw('(ongkir * 0.1) + (total_admin_lokasi * 0.5)'));

        // 2. Order Hari Ini
        $order_hari_ini = DB::table('pesanan_jastip')
            ->whereDate('waktu_order', date('Y-m-d'))
            ->count();

        // 3. Driver Aktif
        $driver_aktif = DB::table('mitra_jastip')
            ->where('status_kerja', 'Aktif')
            ->count();

        // 4. Total Pending (Mencari Driver)
        $total_pending = DB::table('pesanan_jastip')
            ->where('status_jastip', 'Mencari Driver')
            ->count();

        // 5. Ambil Semua Data Jastip
        $all_jastip = DB::table('pesanan_jastip as pj')
            ->join('pelanggan as pl', 'pj.id_pelanggan', '=', 'pl.id_pelanggan')
            ->leftJoin('mitra_jastip as mj', 'pj.id_mitra', '=', 'mj.id_driver')
            ->select('pj.*', 'pl.nama_pelanggan', 'pl.no_wa', 'mj.nama_driver')
            ->orderBy('pj.waktu_order', 'desc')
            ->get();

        return view('admin.jastip', compact(
            'cuan_zasha', 'order_hari_ini', 'driver_aktif', 'total_pending', 'all_jastip'
        ));
    }
}