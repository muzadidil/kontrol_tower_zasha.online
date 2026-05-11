<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminJastipController extends Controller
{
    public function index()
    {
        // 1. Cuan Zasha — komisi sudah dihitung & disimpan saat order dibuat
        $cuan_zasha = DB::table('jastip_orders')
            ->where('status', 'selesai')
            ->sum('komisi_zasha');

        // 2. Order Hari Ini
        $order_hari_ini = DB::table('jastip_orders')
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

        // 3. Driver Aktif — mitra dengan kategori_kode = 'JST' dan status_online aktif
        $driver_aktif = DB::table('mitra')
            ->where('kategori_kode', 'JST')
            ->where('status_online', 'online')
            ->count();

        // 4. Total Pending (menunggu mitra menerima)
        $total_pending = DB::table('jastip_orders')
            ->where('status', 'menunggu_mitra')
            ->count();

        // 5. Ambil Semua Data Jastip
        $all_jastip = DB::table('jastip_orders as jo')
            ->join('pelanggan as pl', 'jo.pelanggan_id', '=', 'pl.id_pelanggan')
            ->leftJoin('mitra as m', 'jo.mitra_id', '=', 'm.id_mitra')
            ->select(
                'jo.id as id_jastip',
                'jo.pelanggan_id as id_pelanggan',
                'jo.mitra_id as id_mitra',
                'jo.status as status_jastip',
                'jo.ongkos_jasa as ongkir',
                'jo.actual_total_barang as total_harga_barang',
                'jo.komisi_zasha as total_admin_lokasi',
                'jo.created_at as waktu_order',
                'pl.nama_pelanggan',
                'pl.no_wa',
                'm.nama_panggilan as nama_driver'
            )
            ->orderByDesc('jo.created_at')
            ->get();

        return view('admin.jastip', compact(
            'cuan_zasha', 'order_hari_ini', 'driver_aktif', 'total_pending', 'all_jastip'
        ));
    }
}
