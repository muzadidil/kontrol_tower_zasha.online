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

    public function updateItem(Request $request, $id)
    {
        $item = \App\Models\OrderItem::findOrFail($id);
        $order = \App\Models\Order::findOrFail($item->order_id);

        $item->harga_asli = $request->harga_asli;
        $item->status_beli = true;
        $item->save();

        // Update total_harga_barang di orders
        $order->total_harga_barang = $order->items()->sum('harga_asli');
        $order->save();

        return back()->with('success', 'Barang berhasil di-update.');
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

        if ($order->kategori == 'Jastip') {
            // Validasi saldo untuk Jastip COD
            if ($order->metode_pembayaran == 'COD') {
                $komisi = $order->ongkos_jastip * 0.05;
                if ($mitra->saldo_mitra < $komisi) {
                    return back()->withErrors(['error' => 'Saldo mitra tidak cukup untuk membayar komisi Jastip.']);
                }
                // Potong saldo mitra sebesar 5% dari ongkos jastip
                $mitra->saldo_mitra -= $komisi;
                $mitra->save();
            }
        } elseif ($mitra->is_wfh) {
            // Escrow: 95% ke mitra, 5% ke platform
            $pendapatan_mitra = $order->total_biaya * 0.95;
            // ... (pendapatan platform logic)
            $mitra->saldo_mitra += $pendapatan_mitra;
            $mitra->save();
        } else {
            // ... (existing)
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

