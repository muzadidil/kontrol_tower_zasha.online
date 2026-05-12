<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminArsipPesananController extends Controller
{
    public function index(Request $request)
    {
        $filter_status = $request->query('status', '');
        $filter_nama = $request->query('nama', '');
        $search = $request->query('search', '');

        // Pakai 'pelanggans' (plural) - tabel utama untuk semua data pelanggan.
        $query = DB::table('pesanan')
            ->join('pelanggans', 'pesanan.id_pelanggan', '=', 'pelanggans.id_pelanggan')
            ->leftJoin('mitra', 'pesanan.id_mitra', '=', 'mitra.id_mitra')
            ->select(
                'pesanan.*',
                DB::raw('COALESCE(pelanggans.nama_panggilan, pelanggans.nama_pelanggan, "Pelanggan") as nama_pelanggan'),
                DB::raw('COALESCE(mitra.nama_asli, mitra.nama_panggilan, "-") as nama_mitra')
            )
            ->whereIn('pesanan.status_pesanan', ['Selesai', 'Batal']);

        if ($filter_status != '') {
            $query->where('pesanan.status_pesanan', $filter_status);
        }
        if ($filter_nama != '') {
            $query->where('pelanggans.nama_pelanggan', 'like', '%' . Str::escapeLike($filter_nama) . '%');
        }
        if ($search != '') {
            $safe = Str::escapeLike($search);
            $query->where(function ($q) use ($safe) {
                $q->where('pesanan.id_pesanan', 'like', '%' . $safe . '%')
                  ->orWhere('pelanggans.nama_pelanggan', 'like', '%' . $safe . '%')
                  ->orWhere('mitra.nama_asli', 'like', '%' . $safe . '%');
            });
        }

        $riwayat = $query->orderBy('pesanan.id_pesanan', 'desc')->get();

        return view('admin.orders.arsip', compact('riwayat', 'filter_status', 'filter_nama', 'search'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id_pesanan'  => 'required',
            'status_baru' => 'required|string|in:Selesai,Batal',
        ]);

        DB::table('pesanan')
            ->where('id_pesanan', $request->id_pesanan)
            ->update(['status_pesanan' => $request->status_baru]);

        return redirect()->back()->with('pesan', 'Status arsip berhasil dikoreksi!');
    }
}
