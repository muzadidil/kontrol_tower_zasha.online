<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    public function index()
    {
        $user       = auth('pelanggan')->user();
        $categories = DB::table('kategori_pekerjaan')->orderBy('nama_kategori')->get();

        return view('pelanggan.dashboard', compact('user', 'categories'));
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
            'nominal' => 'required|numeric|min:10000|max:10000000',
        ]);

        DB::table('dompet_pelanggan')->insert([
            'id_pelanggan'  => auth('pelanggan')->id(),
            'nominal'       => $request->nominal,
            'status'        => 'pending',
            'waktu_request' => now(),
        ]);

        return back()->with('success', 'Request topup Rp ' . number_format($request->nominal, 0, ',', '.') . ' berhasil. Silakan transfer sesuai nominal.');
    }

    public function invoice($id)
    {
        $d = DB::table('pesanan as p')
            ->join('mitra as m', 'p.id_mitra', '=', 'm.id_mitra')
            ->join('pelanggan as pl', 'p.id_pelanggan', '=', 'pl.id_pelanggan')
            ->select('p.*', 'm.nama_mitra', 'pl.nama_pelanggan')
            ->where('p.id_pesanan', $id)
            ->first();
        return view('pelanggan.invoice', compact('d'));
    }

    public function cekStatus($id)
    {
        $status = DB::table('pesanan')->where('id_pesanan', $id)->value('status_pesanan');
        return response()->json(['status' => $status ?? 'tidak_ditemukan']);
    }

    public function katalog($id_kategori)
    {
        $kategori = DB::table('kategori_pekerjaan')->where('id_kategori', $id_kategori)->first();
        $mitras   = DB::table('mitras')
            ->where('kategori', $id_kategori)
            ->where('status', 'Aktif')
            ->get();
        return view('pelanggan.katalog', compact('kategori', 'mitras'));
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

    public function simpanPesanan(Request $request)
    {
        $id_pelanggan = auth('pelanggan')->id();
        $mitra        = DB::table('mitra')->where('id_mitra', $request->id_mitra)->first();

        $id_pesanan = DB::table('pesanan')->insertGetId([
            'id_pelanggan'    => $id_pelanggan,
            'id_mitra'        => $request->id_mitra,
            'id_kategori'     => $mitra->id_kategori,
            'tanggal_pesanan' => now(),
            'total_pesanan'   => ($mitra->tarif_per_jam * $request->durasi) + 5000 + rand(111, 999),
            'status_pesanan'  => 'Pending',
        ]);

        return redirect()->route('pelanggan.invoice', $id_pesanan);
    }
}
