<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\{MitraLayanan, TenagaOrder};
use App\Services\TenagaService;
use Illuminate\Http\Request;

class TenagaController extends Controller
{
    public function __construct(private TenagaService $tenagaService) {}

    public function create(Request $request)
    {
        $layanan = MitraLayanan::with('mitra', 'masterLayanan')->findOrFail($request->layanan);
        return view('pelanggan.tenaga.create', compact('layanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mitra_layanan_id'   => 'required|exists:mitra_layanans,id',
            'tipe_waktu'         => 'required|in:instan,terjadwal',
            'jadwal_at'          => 'required_if:tipe_waktu,terjadwal|nullable|date',
            'tipe_durasi'        => 'required|in:jam,hari',
            'durasi'             => 'required|numeric|min:0.5',
            'metode_pembayaran'  => 'required|in:cod,saldo,transfer',
            'alamat_pelanggan'   => 'required|string',
            'pelanggan_lat'      => 'nullable|numeric',
            'pelanggan_lng'      => 'nullable|numeric',
            'keterangan_kerja'   => 'nullable|string',
        ]);

        try {
            $order = $this->tenagaService->buatOrder($request->all(), auth('pelanggan')->id());
            return redirect()->route('pelanggan.tenaga.show', $order->id)->with('success', 'Order dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $orders = TenagaOrder::where('pelanggan_id', auth('pelanggan')->id())->latest()->paginate(10);
        return view('pelanggan.tenaga.index', compact('orders'));
    }

    public function show(TenagaOrder $tenagaOrder)
    {
        $this->authorizePelanggan($tenagaOrder);
        $tenagaOrder->load(['mitra', 'mitraLayanan.masterLayanan']);
        return view('pelanggan.tenaga.show', ['order' => $tenagaOrder]);
    }

    public function konfirmasi(TenagaOrder $tenagaOrder)
    {
        $this->authorizePelanggan($tenagaOrder);
        try {
            $this->tenagaService->konfirmasiTerima($tenagaOrder, auth('pelanggan')->id());
            return back()->with('success', 'Terima kasih, order selesai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dispute(Request $request, TenagaOrder $tenagaOrder)
    {
        $this->authorizePelanggan($tenagaOrder);
        $request->validate(['deskripsi' => 'required|min:20']);
        try {
            $this->tenagaService->bukaDispute($tenagaOrder, auth('pelanggan')->id(), $request->deskripsi);
            return back()->with('warning', 'Dispute terbuka.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizePelanggan(TenagaOrder $order): void
    {
        if ((int) $order->pelanggan_id !== auth('pelanggan')->id()) abort(403);
    }
}
