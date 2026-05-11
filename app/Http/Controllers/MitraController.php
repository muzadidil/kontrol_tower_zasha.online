<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Mitra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $mitras     = Mitra::orderBy('created_at', 'desc')->get();
        $categories = DB::table('kategori_pekerjaan')->orderBy('id_kategori', 'desc')->get();
        $units      = DB::table('master_satuan')->orderBy('nama_satuan')->get();

        // Verifikasi — driver = mitra dengan kategori_kode='JST'
        $list_driver = DB::table('mitra')
            ->where('kategori_kode', 'JST')
            ->where('status_verifikasi', 'pending_review')
            ->get();
        $list_mitra_verif = DB::table('mitra')
            ->where(function ($q) {
                $q->where('status_verifikasi', 'pending_review')
                  ->orWhereNotNull('plat_pengajuan');
            })
            ->get();

        // Jastip — pakai jastip_orders & komisi_zasha dari snapshot saat order dibuat
        $cuan_zasha     = DB::table('jastip_orders')->where('status', 'selesai')->sum('komisi_zasha');
        $order_hari_ini = DB::table('jastip_orders')->whereDate('created_at', date('Y-m-d'))->count();
        $driver_aktif   = DB::table('mitra')->where('kategori_kode', 'JST')->where('status_online', 'online')->count();
        $total_pending  = DB::table('jastip_orders')->where('status', 'menunggu_mitra')->count();
        $all_jastip     = DB::table('jastip_orders as jo')
                            ->join('pelanggans as pl', 'jo.pelanggan_id', '=', 'pl.id_pelanggan')
                            ->leftJoin('mitra as m', 'jo.mitra_id', '=', 'm.id_mitra')
                            ->select(
                                'jo.id as id_jastip',
                                'jo.pelanggan_id as id_pelanggan',
                                'jo.mitra_id as id_mitra',
                                'jo.status as status_jastip',
                                'jo.ongkos_jasa as ongkir',
                                'jo.actual_total_barang as total_harga_barang',
                                'jo.komisi_zasha as total_admin_lokasi',
                                'jo.created_at as waktu_order',
                                'pl.nama_pelanggan', 'pl.no_wa',
                                'm.nama_panggilan as nama_driver'
                            )
                            ->orderByDesc('jo.created_at')
                            ->get();

        // Radar
        $radar_mitras = DB::table('mitra')->orderByDesc('updated_at')->get();

        return view('admin.mitra.index', compact(
            'mitras', 'categories', 'units',
            'list_driver', 'list_mitra_verif',
            'cuan_zasha', 'order_hari_ini', 'driver_aktif', 'total_pending', 'all_jastip',
            'radar_mitras'
        ));
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
            'nama_panggilan' => 'required',
            'no_wa'          => 'required',
        ]);

        Mitra::create($request->only([
            'id_kategori', 'nama_panggilan', 'nama_asli', 'no_wa',
            'tarif_per_jam', 'tarif_per_hari', 'biaya_service_standar',
            'status_mitra', 'deskripsi_singkat', 'alamat',
        ]));
        return redirect()->route('mitra.index')->with('success', 'Mitra berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mitra = Mitra::findOrFail($id);
        return view('admin.mitra.edit', compact('mitra'));
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
            'nama_panggilan' => 'required',
            'no_wa'          => 'required',
        ]);

        $mitra = Mitra::findOrFail($id);
        $mitra->update($request->only([
            'id_kategori', 'nama_panggilan', 'nama_asli', 'no_wa',
            'tarif_per_jam', 'tarif_per_hari', 'biaya_service_standar',
            'status_mitra', 'deskripsi_singkat', 'alamat',
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
        $mitraId = auth('mitra')->id();
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
