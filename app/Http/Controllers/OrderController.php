<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Mitra;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $pelanggans = Pelanggan::all();
        $mitras = Mitra::where('status', 'Aktif')->get();
        return view('admin.order.create', compact('pelanggans', 'mitras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required',
            'mitra_id' => 'required',
            'tipe_waktu' => 'required',
            'durasi_kerja' => 'required|integer',
            'metode_pembayaran' => 'required',
        ]);

        $mitra = Mitra::findOrFail($request->mitra_id);
        
        // Kalkulasi biaya
        $tarif = ($request->durasi_tipe == 'jam') ? $mitra->tarif_per_jam : $mitra->tarif_per_hari;
        $total_biaya = $tarif * $request->durasi_kerja;
        $komisi_zasha = $total_biaya * 0.05;

        // Validasi COD
        if ($request->metode_pembayaran == 'COD' && $mitra->saldo_mitra < $komisi_zasha) {
            return back()->withErrors(['metode_pembayaran' => 'Saldo mitra tidak cukup untuk menutupi komisi Zasha jika COD. Pilih metode lain.']);
        }

        Order::create([
            'pelanggan_id' => $request->pelanggan_id,
            'mitra_id' => $request->mitra_id,
            'tipe_waktu' => $request->tipe_waktu,
            'durasi_kerja' => $request->durasi_kerja,
            'total_biaya' => $total_biaya,
            'komisi_zasha' => $komisi_zasha,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status' => 'Pending',
            'jadwal_pelaksanaan' => $request->jadwal_pelaksanaan,
            'keterangan_kerja' => $request->keterangan_kerja
        ]);

        return redirect()->route('admin.order.index')->with('success', 'Order berhasil dibuat.');
    }
}
