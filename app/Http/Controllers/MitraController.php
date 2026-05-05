<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    public function dashboard()
    {
        // Contoh, asumsikan mitra sedang login dengan ID 1 untuk testing.
        // Seharusnya menggunakan Auth::id() atau middleware yang sesuai.
        $mitraId = 1; 
        $activeOrder = Order::where('mitra_id', $mitraId)
                            ->whereIn('status', ['Pending', 'Menuju Lokasi'])
                            ->first();

        return view('admin.mitra.dashboard', compact('activeOrder'));
    }

    public function tambahItemService(Request $request, $order_id)
    {
        \App\Models\OrderItem::create([
            'order_id' => $order_id,
            'nama_barang' => $request->nama_barang,
            'lokasi_beli' => 'Service',
            'harga_perkiraan' => $request->harga_asli,
            'harga_asli' => $request->harga_asli,
            'status_beli' => true,
            'tipe_item' => $request->tipe_item,
        ]);

        return back()->with('success', 'Item service berhasil ditambahkan.');
    }

    public function index()
    {
        return view('admin.mitra.index');
    }
}
