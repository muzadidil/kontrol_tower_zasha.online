<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use App\Services\ServiceJasaService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(private ServiceJasaService $serviceJasa) {}

    public function index()
    {
        $orders = ServiceOrder::where('mitra_id', auth('mitra')->user()->id_mitra)->latest()->paginate(15);
        return view('mitra.service.index', compact('orders'));
    }

    public function show(ServiceOrder $serviceOrder)
    {
        $this->authorizeMitra($serviceOrder);
        $serviceOrder->load(['pelanggan', 'mitraLayanan.masterLayanan', 'orderItems']);
        return view('mitra.service.show', ['order' => $serviceOrder]);
    }

    public function terima(ServiceOrder $serviceOrder)
    {
        $this->authorizeMitra($serviceOrder);
        try {
            $this->serviceJasa->terimaOrder($serviceOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Order diterima.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }

    public function tolak(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeMitra($serviceOrder);
        $request->validate(['alasan' => 'required|min:10']);
        try {
            $this->serviceJasa->tolakOrder($serviceOrder, auth('mitra')->user()->id_mitra, $request->alasan);
            return redirect()->route('mitra.service.index')->with('info', 'Order ditolak.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }

    public function mulaiDiagnosa(ServiceOrder $serviceOrder)
    {
        $this->authorizeMitra($serviceOrder);
        try {
            $this->serviceJasa->mulaiDiagnosa($serviceOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Mulai diagnosa.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }

    public function submitDiagnosa(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorizeMitra($serviceOrder);
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.tipe' => 'required|in:jasa,sparepart',
            'items.*.nama_item' => 'required|string',
            'items.*.harga' => 'required|numeric|min:0',
        ]);
        try {
            $this->serviceJasa->submitDiagnosa($serviceOrder, auth('mitra')->user()->id_mitra, $request->items);
            return back()->with('success', 'Diagnosa terkirim. Menunggu approval pelanggan.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }

    public function selesaiKerja(ServiceOrder $serviceOrder)
    {
        $this->authorizeMitra($serviceOrder);
        try {
            $this->serviceJasa->selesaiKerja($serviceOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Pekerjaan selesai.');
        } catch (\Exception $e) { return back()->with('error', $e->getMessage()); }
    }

    private function authorizeMitra(ServiceOrder $order): void
    {
        if ((int) $order->mitra_id !== auth('mitra')->user()->id_mitra) abort(403);
    }
}
