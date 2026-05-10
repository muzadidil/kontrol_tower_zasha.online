<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\NotifHelper;

class PelangganController extends Controller
{
    public function index()
    {
        $user = auth('pelanggan')->user();
        if (!$user) return redirect()->route('login');

        $categories   = DB::table('kategori_pekerjaan')->orderBy('nama_kategori')->get();
        $unread_notif = NotifHelper::unreadCount($user->id_pelanggan);

        return view('pelanggan.dashboard', compact('user', 'categories', 'unread_notif'));
    }

    public function dompet()
    {
        $id_p      = auth('pelanggan')->id();
        $pelanggan = DB::table('pelanggans')->where('id_pelanggan', $id_p)->first();
        $riwayat   = DB::table('dompet_pelanggan')->where('id_pelanggan', $id_p)->orderBy('waktu_request', 'desc')->get();
        return view('pelanggan.dompet', compact('pelanggan', 'riwayat'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'nominal'     => 'required|numeric|min:10000|max:10000000',
            'bank_tujuan' => 'required|in:DANA,BCA',
        ]);

        $id_p = auth('pelanggan')->id();

        // Bersihkan format nominal (hapus titik, koma, spasi) lalu cast ke integer
        $rawNominal = $request->input('nominal');
        $nominal    = (int) preg_replace('/[^0-9]/', '', $rawNominal);

        // Pastikan nominal valid setelah parsing
        if ($nominal < 10000) {
            return back()->withErrors(['nominal' => 'Nominal minimal Rp 10.000'])->withInput();
        }

        // Generate kode unik 3 digit (100-999) untuk identifikasi transfer
        $kode_unik = rand(100, 999);

        // Hitung total transfer = nominal + kode_unik
        $total_transfer = $nominal + $kode_unik;

        DB::table('dompet_pelanggan')->insert([
            'id_pelanggan'   => (int) $id_p,
            'nominal'        => $nominal,
            'kode_unik'      => $kode_unik,
            'total_transfer' => $total_transfer,
            'bank_tujuan'    => $request->input('bank_tujuan'),
            'status'         => 'pending',
            'waktu_request'  => now(),
        ]);

        NotifHelper::kirim(
            $id_p,
            'Request Top-Up Diterima',
            'Request top-up sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' sedang diproses. Transfer tepat Rp ' . number_format($total_transfer, 0, ',', '.') . ' agar otomatis terdeteksi.',
            'topup',
            route('pelanggan.dompet')
        );

        return back()
            ->with('notif_topup', 'sukses')
            ->with('data_nominal', $nominal)
            ->with('data_kode_unik', $kode_unik)
            ->with('data_transfer', $total_transfer)
            ->with('data_bank', $request->input('bank_tujuan'));
    }

    public function invoice($id)
    {
        $d = DB::table('pesanan as p')
            ->join('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->join('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->select('p.*', 'm.nama_asli as nama_mitra', 'pl.nama_pelanggan')
            ->where('p.id_pesanan', $id)
            ->first();
        return view('pelanggan.invoice', compact('d'));
    }

    public function cekStatus($id)
    {
        $status = DB::table('pesanan')->where('id_pesanan', $id)->value('status_pesanan');
        return response()->json(['status' => $status ?? 'tidak_ditemukan']);
    }

    public function katalog(Request $request, $id_kategori)
    {
        $kategori = DB::table('kategori_pekerjaan')->where('id_kategori', $id_kategori)->first();
        if (!$kategori) abort(404);

        $sort      = $request->get('sort', 'bintang');
        $is_jastip = false;

        $query = DB::table('mitra as m')
            ->leftJoin('pesanan_mitra as pm', 'm.id_mitra', '=', 'pm.id_mitra')
            ->leftJoin('kategori_pekerjaan as k', 'm.id_kategori', '=', 'k.id_kategori')
            ->select(
                'm.id_mitra',
                'm.nama_panggilan as nama_mitra',
                'm.foto_mitra',
                'm.status_mitra',
                DB::raw('COALESCE(m.tarif_per_jam, 0) as tarif_per_jam'),
                DB::raw('0 as jarak'),
                DB::raw('COALESCE(k.satuan, "Jam") as satuan_tarif'),
                DB::raw('COALESCE(AVG(pm.rating), 0) as rating_rata'),
                DB::raw('COUNT(pm.id_pesanan) as total_order')
            )
            ->where('m.id_kategori', $id_kategori)
            ->groupBy('m.id_mitra', 'm.nama_panggilan', 'm.foto_mitra', 'm.status_mitra', 'm.tarif_per_jam', 'k.satuan');

        if ($sort == 'orderan') {
            $query->orderByDesc('total_order');
        } elseif ($sort == 'bintang') {
            $query->orderByDesc('rating_rata');
        } else {
            $query->orderBy('m.id_mitra');
        }

        $mitras = $query->get();

        return view('pelanggan.katalog', [
            'kategori'    => $kategori,
            'mitras'      => $mitras,
            'nama_kat'    => $kategori->nama_kategori ?? 'Katalog',
            'id_kategori' => $id_kategori,
            'sort'        => $sort,
            'is_jastip'   => $is_jastip,
        ]);
    }

    public function review($id_order)
    {
        $order = DB::table('pesanan as p')
            ->leftJoin('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->select('p.id_pesanan as id_order', 'm.nama_panggilan as nama_mitra', 'p.id_mitra')
            ->where('p.id_pesanan', $id_order)
            ->where('p.id_pelanggan', auth('pelanggan')->id())
            ->first();
        return view('pelanggan.review', compact('order'));
    }

    public function kirimReview(Request $request)
    {
        $request->validate([
            'id_order' => 'required',
            'id_mitra' => 'required',
            'rating'   => 'required|integer|min:1|max:5',
            'ulasan'   => 'nullable|string|max:500',
        ]);

        DB::table('pesanan')
            ->where('id_pesanan', $request->id_order)
            ->where('id_pelanggan', auth('pelanggan')->id())
            ->update([
                'rating' => $request->rating,
                'ulasan' => $request->ulasan,
            ]);

        return redirect()->route('pelanggan.riwayat.index')->with('success', 'Terima kasih! Ulasan berhasil dikirim.');
    }

    public function detailMitra($id)
    {
        $mitra = DB::table('mitra as m')
            ->leftJoin('kategori_pekerjaan as k', 'm.id_kategori', '=', 'k.id_kategori')
            ->select('m.*', 'k.nama_kategori', DB::raw('COALESCE(k.satuan, "Jam") as satuan_tarif'))
            ->where('m.id_mitra', $id)
            ->first();

        if (!$mitra) abort(404);

        $rating_rata = DB::table('pesanan_mitra')
            ->where('id_mitra', $id)
            ->whereNotNull('rating')
            ->where('rating', '>', 0)
            ->avg('rating') ?? 0;

        $total_order = DB::table('pesanan_mitra')->where('id_mitra', $id)->count();

        $ulasans = DB::table('pesanan_mitra as pm')
            ->join('pelanggan as pl', 'pm.id_pelanggan', '=', 'pl.id_pelanggan')
            ->select('pm.rating', 'pm.ulasan', 'pl.nama_pelanggan', 'pm.id_pesanan')
            ->where('pm.id_mitra', $id)
            ->whereNotNull('pm.ulasan')
            ->orderBy('pm.id_pesanan', 'desc')
            ->limit(5)
            ->get();

        $alamats = auth('pelanggan')->check()
            ? DB::table('alamats')->where('id_pelanggan', auth('pelanggan')->id())->orderByDesc('is_utama')->get()
            : collect();

        return view('pelanggan.detail-mitra', compact('mitra', 'rating_rata', 'total_order', 'ulasans', 'alamats'));
    }

    public function detailJastip($id)
    {
        $driver = DB::table('mitra_jastip')->where('id_driver', $id)->first();
        if (!$driver) abort(404);

        return view('pelanggan.detail-jastip', compact('driver'));
    }

    public function simpanPesanan(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();
        $mitra        = DB::table('mitra')->where('id_mitra', $request->id_mitra)->first();

        if (!$mitra) {
            return back()->with('error', 'Mitra tidak ditemukan.');
        }

        $id_pesanan = DB::table('pesanan')->insertGetId([
            'id_pelanggan'    => $id_pelanggan,
            'id_mitra'        => $request->id_mitra,
            'id_kategori'     => $mitra->id_kategori,
            'tanggal_pesanan' => now(),
            'total_pesanan'   => ($mitra->tarif_per_jam * $request->durasi) + 5000,
            'status_pesanan'  => 'Pending',
        ]);

        NotifHelper::kirim(
            $id_pelanggan,
            'Pesanan Berhasil Dibuat',
            'Pesanan #' . $id_pesanan . ' ke mitra ' . $mitra->nama_panggilan . ' telah dibuat dan sedang menunggu konfirmasi.',
            'pesanan',
            route('pelanggan.riwayat.index')
        );

        return redirect()->route('pelanggan.invoice', $id_pesanan);
    }

    public function notifikasi()
    {
        $id_p = auth('pelanggan')->id();

        $notifikasi = DB::table('notifikasi')
            ->where('id_pelanggan', $id_p)
            ->orderByDesc('created_at')
            ->get();

        NotifHelper::tandaiSemuaDibaca($id_p);

        return view('pelanggan.notifikasi', compact('notifikasi'));
    }
}
