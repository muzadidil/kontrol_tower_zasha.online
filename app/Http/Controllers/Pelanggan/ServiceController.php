<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\{MitraLayanan, ServiceOrder};
use App\Services\ServiceJasaService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(private ServiceJasaService $serviceJasa) {}

    public function create(Request $request)
    {
        $layanan = MitraLayanan::with('mitra', 'masterLayanan')->findOrFail($request->layanan);
        return view('pelanggan.service.create', compact('layanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mitra_layanan_id'  => 'required|exists:mitra_layanans,id',
            'jarak_km'          => 'nullable|numeric',
            'alamat_pelanggan'  => 'required|string',
            'pelanggan_lat'     => 'nullable|numeric',
            'pelanggan_lng'     => 'nullable|numeric',
            'keluhan_pelanggan' => 'required|string|min:20',
        ]);

        try {
            $order = $this->serviceJasa->buatOrder($request->all(), auth('pelanggan')->id());
            return redirect()->route('pelanggan.service.show', $order->id)->with('success', 'Order Service dibuat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function index()
    {
        $orders = ServiceOrder::where('pelanggan_id', auth('pelanggan')->id())->latest()->paginate(10);
        return view('pelanggan.service.index', compact('orders'));
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $this->authorizePelanggan($serviceOrder);
        $serviceOrder->load(['mitra', 'mitraLayanan.masterLayanan', 'orderItems']);
        return view('pelanggan.service.show', ['order' => $serviceOrder]);
    }

    public function approveHarga(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizePelanggan($serviceOrder);
        $request->validate(['metode_pembayaran' => 'required|in:cod,saldo,transfer']);
        try {
            $this->serviceJasa->approveHarga($serviceOrder, auth('pelanggan')->id(), $request->metode_pembayaran);
            return back()->with('success', 'Harga disetujui, mitra akan mulai bekerja.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function konfirmasi(ServiceOrder $serviceOrder)
    {
        $this->authorizePelanggan($serviceOrder);
        try {
            $this->serviceJasa->konfirmasiTerima($serviceOrder, auth('pelanggan')->id());
            return back()->with('success', 'Order Service selesai.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dispute(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizePelanggan($serviceOrder);
        $request->validate(['deskripsi' => 'required|min:20']);
        try {
            $this->serviceJasa->bukaDispute($serviceOrder, auth('pelanggan')->id(), $request->deskripsi);
            return back()->with('warning', 'Dispute terbuka.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizePelanggan(ServiceOrder $order): void
    {
        if ((int) $order->pelanggan_id !== auth('pelanggan')->id()) abort(403);
    }
}
