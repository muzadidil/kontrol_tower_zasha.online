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
            'pelanggan_id'       => 'required',
            'mitra_id'           => 'required',
            'metode_pembayaran'  => 'required|in:COD,Transfer,Saldo',
            'jarak_km'           => 'nullable|numeric|min:0',
            'durasi_kerja'       => 'nullable|numeric|min:0',
        ]);

        $mitra = Mitra::findOrFail($request->mitra_id);

        if ($mitra->is_wfh && $request->metode_pembayaran == 'COD') {
            return back()->withErrors(['metode_pembayaran' => 'Jasa WFH hanya mendukung pembayaran non-tunai/saldo']);
        }

        $jarak_km = $request->jarak_km ?? 0;
        $biaya_bensin = $jarak_km > 5 ? ($jarak_km - 5) * $mitra->tarif_bensin_per_km_service : 0;
        
        $total_biaya = $mitra->biaya_service_standar + $biaya_bensin;

        $order = Order::create([
            'pelanggan_id' => $request->pelanggan_id,
            'mitra_id' => $request->mitra_id,
            'status' => 'Pending',
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_biaya' => $total_biaya,
            'durasi_kerja' => $request->durasi_kerja ?? 0,
            'komisi_zasha' => 0,
            'jarak_km' => $jarak_km,
            'biaya_bensin_service' => $biaya_bensin,
            'tipe_waktu' => 'Instan',
        ]);

        return redirect()->back()->with('success', 'Order created.');
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

        $total_jasa = $order->items()->where('tipe_item', 'jasa')->sum('harga_asli');
        $komisi_zasha = ($total_jasa + $order->biaya_bensin_service) * 0.05;

        if ($order->metode_pembayaran == 'COD' && $komisi_zasha > $mitra->saldo) {
            return back()->withErrors(['error' => 'Saldo tidak cukup, mohon gunakan pembayaran Transfer/Cashless.']);
        }

        if ($order->kategori == 'Jastip') {
            // Validasi saldo untuk Jastip COD
            if ($order->metode_pembayaran == 'COD') {
                $komisi = $order->ongkos_jastip * 0.05;
                if ($mitra->saldo < $komisi) {
                    return back()->withErrors(['error' => 'Saldo mitra tidak cukup untuk membayar komisi Jastip.']);
                }
                // Potong saldo mitra sebesar 5% dari ongkos jastip
                $mitra->saldo -= $komisi;
                $mitra->save();
            }
        } elseif ($mitra->is_wfh) {
            // Escrow: 95% ke mitra, 5% ke platform
            $pendapatan_mitra = $order->total_biaya * 0.95;
            // ... (pendapatan platform logic)
            $mitra->saldo += $pendapatan_mitra;
            $mitra->save();
        } else {
            // New logic for Service Module
            if ($order->metode_pembayaran == 'COD') {
                $mitra->saldo -= $komisi_zasha;
                $mitra->save();
            }
        }

        $order->status = 'Selesai';
        $order->komisi_zasha = $komisi_zasha;
        $order->save();

        return back()->with('success', 'Pesanan selesai.');
    }
}

