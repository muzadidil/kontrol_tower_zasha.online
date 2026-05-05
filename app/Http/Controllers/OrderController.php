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
        $mitra = Mitra::findOrFail($request->mitra_id);

        if ($mitra->is_wfh && $request->metode_pembayaran == 'COD') {
            return back()->withErrors(['metode_pembayaran' => 'Jasa WFH hanya mendukung pembayaran non-tunai/saldo']);
        }

        // ... existing store logic (assumed to exist) ...
    }

    public function updateStatus(Request $request, $id)
    {
        // ... (existing)
    }

    public function tracker($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.pelanggan.tracker', compact('order'));
    }

    public function konfirmasiSelesai($id)
    {
        $order = Order::findOrFail($id);
        $mitra = Mitra::findOrFail($order->mitra_id);

        if ($mitra->is_wfh) {
            // Escrow: 95% ke mitra, 5% ke platform
            $pendapatan_mitra = $order->total_biaya * 0.95;
            $pendapatan_platform = $order->total_biaya * 0.05;

            $mitra->saldo_mitra += $pendapatan_mitra;
            $mitra->save();
            
            // Logika platform income bisa ditambahkan di sini jika ada model Keuangan
        } else {
            $order->status = 'Selesai';
            $order->save();

            if ($order->metode_pembayaran == 'COD') {
                $mitra->saldo_mitra -= $order->komisi_zasha;
                $mitra->save();
            }
        }

        $order->status = 'Selesai';
        $order->save();

        return back()->with('success', 'Pesanan selesai.');
    }
}

