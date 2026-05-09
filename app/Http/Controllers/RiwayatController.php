<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    private function getBadgeColor($status)
    {
        $s = strtolower(trim($status));
        if (in_array($s, ['menunggu', 'pending'])) return 'bg-warning text-dark';
        if (in_array($s, ['proses', 'berjalan', 'aktif', 'diterima', 'belanja', 'pengiriman'])) return 'bg-primary text-white';
        if ($s == 'selesai') return 'bg-success text-white';
        return 'bg-secondary text-white';
    }

    public function index(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id(); // Menggunakan auth('pelanggan')->id()
        $status_filter = $request->get('status', 'semua');

        // Query Jasa (Pesanan Mitra)
        $query_jasa = DB::table('pesanan_mitra as p')
            ->leftJoin('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->leftJoin('kategori_pekerjaan as k', 'm.id_kategori', '=', 'k.id_kategori')
            ->select('p.*', 'm.nama_panggilan as nama_mitra', 'm.foto_mitra', 'k.nama_kategori')
            ->where('p.id_pelanggan', $id_pelanggan)
            ->when($status_filter != 'semua', function ($q) use ($status_filter) {
                return $q->where('p.status_pesanan', $status_filter);
            })
            ->orderBy('p.id_pesanan', 'desc')
            ->get();

        // Query Jastip
        $query_jastip = DB::table('pesanan_jastip as pj')
            ->leftJoin('mitra_jastip as mj', 'pj.id_mitra', '=', 'mj.id_driver')
            ->select('pj.*', 'mj.nama_driver', 'mj.foto_driver')
            ->where('pj.id_pelanggan', $id_pelanggan)
            ->when($status_filter != 'semua', function ($q) use ($status_filter) {
                return $q->where('pj.status_jastip', $status_filter);
            })
            ->orderBy('pj.id_jastip', 'desc')
            ->get();

        return view('pelanggan.riwayat', [
            'query_jasa' => $query_jasa,
            'query_jastip' => $query_jastip,
            'status_filter' => $status_filter,
            'getBadgeColor' => fn($status) => $this->getBadgeColor($status)
        ]);
    }

    public function updateStatus(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();
        $id_o = $request->id_order;
        $aksi = $request->aksi;
        $type = $request->get('type', 'jasa');

        if ($type == 'jastip') {
            $status_baru = ($aksi == 'selesai') ? 'Selesai' : (($aksi == 'batal') ? 'Dibatalkan' : '');
            if (!empty($status_baru)) {
                DB::table('pesanan_jastip')
                    ->where('id_jastip', $id_o)
                    ->where('id_pelanggan', $id_pelanggan)
                    ->update(['status_jastip' => $status_baru]);
            }
        } else {
            $status_baru = ($aksi == 'selesai') ? 'Selesai' : (($aksi == 'batal') ? 'Batal' : '');
            if (!empty($status_baru)) {
                DB::table('pesanan_mitra')
                    ->where('id_pesanan', $id_o)
                    ->where('id_pelanggan', $id_pelanggan)
                    ->update(['status_pesanan' => $status_baru]);
            }
        }

        return redirect()->route('pelanggan.riwayat.index');
    }

    public function kirimUlasan(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();
        
        DB::table('pesanan_mitra')
            ->where('id_pesanan', $request->id_order)
            ->where('id_pelanggan', $id_pelanggan)
            ->update([
                'rating' => $request->rating_nilai,
                'ulasan' => $request->ulasan_teks
            ]);

        return redirect()->route('pelanggan.riwayat.index');
    }
}