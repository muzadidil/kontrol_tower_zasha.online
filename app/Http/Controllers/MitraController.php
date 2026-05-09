<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Mitra;
use Illuminate\Support\Facades\Auth;

class MitraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mitras = Mitra::all();
        return view('admin.mitra.index', compact('mitras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mitra.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'alamat' => 'required',
            'nomor_wa' => 'required',
        ]);

        Mitra::create($request->only([
            'nama_panggilan', 'usia', 'kategori', 'deskripsi_singkat', 'nomor_wa',
            'tarif_per_jam', 'tarif_per_hari', 'status', 'portfolio_link',
            'is_wfh', 'tarif_per_km', 'biaya_service_standar', 'tarif_bensin_per_km_service',
        ]));
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mitra = Mitra::findOrFail($id);
        return view('admin.mitra.show', compact('mitra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mitra = Mitra::findOrFail($id);
        return view('admin.mitra.edit', compact('mitra'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama' => 'required',
            'kategori' => 'required',
            'alamat' => 'required',
            'nomor_wa' => 'required',
        ]);

        $mitra = Mitra::findOrFail($id);
        $mitra->update($request->only([
            'nama_panggilan', 'usia', 'kategori', 'deskripsi_singkat', 'nomor_wa',
            'tarif_per_jam', 'tarif_per_hari', 'status', 'portfolio_link',
            'is_wfh', 'tarif_per_km', 'biaya_service_standar', 'tarif_bensin_per_km_service',
        ]));
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mitra = Mitra::findOrFail($id);
        $mitra->delete();
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil dihapus.');
    }

    public function dashboard()
    {
        $mitraId = auth()->user()->mitra_id;
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
}
