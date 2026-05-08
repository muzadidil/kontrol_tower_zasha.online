<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminArsipPesananController extends Controller
{
    public function index(Request $request)
    {
        $filter_status = $request->query('status', '');
        $filter_nama = $request->query('nama', '');
        $search = $request->query('search', '');

        $query = DB::table('pesanan')
            ->join('pelanggan', 'pesanan.id_pelanggan', '=', 'pelanggan.id_pelanggan')
            ->leftJoin('mitra', 'pesanan.id_mitra', '=', 'mitra.id_mitra')
            ->select('pesanan.*', 'pelanggan.nama_pelanggan', 'mitra.nama_mitra')
            ->whereIn('pesanan.status_pesanan', ['Selesai', 'Batal']);

        if ($filter_status != '') {
            $query->where('pesanan.status_pesanan', $filter_status);
        }
        if ($filter_nama != '') {
            $query->where('pelanggan.nama_pelanggan', 'like', "%$filter_nama%");
        }
        if ($search != '') {
            $query->where(function($q) use ($search) {
                $q->where('pesanan.id_pesanan', 'like', "%$search%")
                  ->orWhere('pelanggan.nama_pelanggan', 'like', "%$search%")
                  ->orWhere('mitra.nama_mitra', 'like', "%$search%");
            });
        }

        $riwayat = $query->orderBy('pesanan.id_pesanan', 'desc')->get();

        return view('admin.orders.arsip', compact('riwayat', 'filter_status', 'filter_nama', 'search'));
    }

    public function updateStatus(Request $request)
    {
        DB::table('pesanan')
            ->where('id_pesanan', $request->id_pesanan)
            ->update(['status_pesanan' => $request->status_baru]);

        return redirect()->back()->with('pesan', 'Status arsip berhasil dikoreksi!');
    }
}
