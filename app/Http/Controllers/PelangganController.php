<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    public function index()
    {
        return view('pelanggan.dashboard');
    }

    public function alamat()
    {
        $id_cust = Auth::id() ?? session('id_pelanggan'); 
        $alamat = DB::table('alamat')->where('id_pelanggan', $id_cust)->orderBy('is_utama', 'desc')->get();
        return view('alamat', compact('alamat'));
    }

    public function review($id_order)
    {
        $order = DB::table('orders as o')
            ->join('mitra as m', 'o.id_mitra', '=', 'm.id_mitra')
            ->select('o.*', 'm.nama_mitra')
            ->where('o.id_order', $id_order)
            ->first();
        if (!$order) return redirect()->back()->with('error', 'Order tidak ditemukan.');
        return view('review', compact('order'));
    }

    public function kirimReview(Request $request)
    {
        $id_pelanggan = Auth::id() ?? session('id_pelanggan');
        DB::table('review_mitra')->insert([
            'id_mitra' => $request->id_mitra,
            'id_pelanggan' => $id_pelanggan,
            'id_order' => $request->id_order,
            'bintang' => $request->rating,
            'komentar' => $request->komentar,
            'created_at' => now()
        ]);
        $new_avg = DB::table('review_mitra')->where('id_mitra', $request->id_mitra)->avg('bintang');
        DB::table('mitra')->where('id_mitra', $request->id_mitra)->update(['rating_avg' => $new_avg]);
        return redirect()->route('pelanggan.profil')->with('success', 'Ulasan terkirim!');
    }

    public function dompet()
    {
        $id_p = Auth::id() ?? session('id_pelanggan');
        $pelanggan = DB::table('pelanggan')->where('id_pelanggan', $id_p)->first();
        $riwayat = DB::table('dompet_pelanggan')->where('id_pelanggan', $id_p)->orderBy('waktu_request', 'desc')->get();
        return view('dompet', compact('pelanggan', 'riwayat'));
    }

    public function topup(Request $request)
    {
        $id_p = Auth::id() ?? session('id_pelanggan');
        $kode_unik = rand(100, 999);
        $total_transfer = $request->nominal + $kode_unik;
        DB::table('dompet_pelanggan')->insert([
            'id_pelanggan' => $id_p,
            'nominal_asli' => $request->nominal,
            'kode_unik' => $kode_unik,
            'total_transfer' => $total_transfer,
            'nomor_rekening' => $request->nomor_rekening,
            'bank_tujuan' => $request->bank_tujuan,
            'status' => 'pending',
            'waktu_request' => now()
        ]);
        return redirect()->route('pelanggan.dompet')->with(['notif_topup' => 'sukses', 'data_transfer' => $total_transfer, 'data_bank' => $request->bank_tujuan]);
    }

    public function invoice($id)
    {
        $d = DB::table('pesanan as p')
            ->join('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->join('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->select('p.*', 'm.nama_mitra', 'pl.nama_pelanggan')
            ->where('p.id_pesanan', $id)
            ->first();
        if (!$d) return "Pesanan tidak ditemukan.";
        return view('invoice', compact('d'));
    }

    public function cekStatus($id)
    {
        $status = DB::table('pesanan')->where('id_pesanan', $id)->value('status_pesanan');
        return response()->json(['status' => $status]);
    }

    // --- FITUR KATALOG MITRA ---
    public function katalog(Request $request, $id_kategori)
    {
        $sort = $request->query('sort', 'jarak');
        $id_p = Auth::id() ?? session('id_pelanggan');

        // 1. Ambil Nama Kategori
        $kat = DB::table('kategori_pekerjaan')->where('id_kategori', $id_kategori)->first();
        $nama_kat = $kat->nama_kategori ?? 'Pilih Mitra';

        // 2. Ambil Koordinat Pelanggan
        $addr = DB::table('alamat_pelanggan')->where('id_pelanggan', $id_p)->orderBy('id_alamat', 'desc')->first();
        $lat_p = $addr->lat ?? -8.184486;
        $lng_p = $addr->lng ?? 113.668075;

        // 3. Tentukan Order By
        $order_by = "status_tampil DESC, jarak ASC";
        if ($sort == 'bintang') $order_by = "status_tampil DESC, rating_rata DESC, jarak ASC";
        elseif ($sort == 'orderan') $order_by = "status_tampil DESC, total_order DESC, jarak ASC";

        // 4. Query Berdasarkan Tipe Kategori
        $is_jastip = (strpos(strtolower($nama_kat), 'jastip') !== false);

        if ($is_jastip) {
            $sql = "SELECT id_driver AS id_mitra, nama_driver AS nama_mitra, foto_driver AS foto_mitra, status_kerja AS status_mitra,
                    'Driver Jastip Terverifikasi' AS keahlian_singkat, rating_rata, total_selesai AS total_order,
                    IF(status_kerja = 'aktif', 1, 0) AS status_tampil,
                    (6371 * acos(cos(radians($lat_p)) * cos(radians(lat)) * cos(radians(lng) - radians($lng_p)) + sin(radians($lat_p)) * sin(radians(lat)))) AS jarak
                    FROM mitra_jastip ORDER BY $order_by";
        } else {
            $sql = "SELECT m.*, m.nama_panggilan AS nama_mitra, m.rating_avg AS rating_rata, m.total_order,
                    IF(m.status_mitra = 'aktif', 1, 0) AS status_tampil,
                    (6371 * acos(cos(radians($lat_p)) * cos(radians(m.lat_mitra)) * cos(radians(m.lng_mitra) - radians($lng_p)) + sin(radians($lat_p)) * sin(radians(m.lat_mitra)))) AS jarak
                    FROM mitra m WHERE m.id_kategori = '$id_kategori' ORDER BY $order_by";
        }

        $mitras = DB::select($sql);

        return view('katalog', compact('mitras', 'nama_kat', 'id_kategori', 'sort', 'is_jastip'));
    }
}
