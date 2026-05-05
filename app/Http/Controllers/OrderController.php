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

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menuju Lokasi,Dikerjakan,Menunggu Konfirmasi,Selesai,Dibatalkan',
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', 'Status order berhasil diupdate.');
    }

    public function tracker($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.pelanggan.tracker', compact('order'));
    }

    public function konfirmasiSelesai($id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'Selesai';
        $order->save();

        if ($order->metode_pembayaran == 'COD') {
            $mitra = Mitra::findOrFail($order->mitra_id);
            $mitra->saldo_mitra -= $order->komisi_zasha;
            $mitra->save();
        }

        return back()->with('success', 'Pesanan selesai dan saldo mitra telah diperbarui.');
    }
}

