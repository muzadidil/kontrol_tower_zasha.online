<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\TenagaOrder;
use App\Services\TenagaService;
use Illuminate\Http\Request;

class TenagaController extends Controller
{
    public function __construct(private TenagaService $tenagaService) {}

    public function index()
    {
        $orders = TenagaOrder::where('mitra_id', auth('mitra')->user()->id_mitra)->latest()->paginate(15);
        return view('mitra.tenaga.index', compact('orders'));
    }

    public function show(TenagaOrder $tenagaOrder)
    {
        $this->authorizeMitra($tenagaOrder);
        $tenagaOrder->load(['pelanggan', 'mitraLayanan.masterLayanan']);
        return view('mitra.tenaga.show', ['order' => $tenagaOrder]);
    }

    public function terima(TenagaOrder $tenagaOrder)
    {
        $this->authorizeMitra($tenagaOrder);
        try {
            $this->tenagaService->terimaOrder($tenagaOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Order diterima, mulai berangkat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function tolak(Request $request, TenagaOrder $tenagaOrder)
    {
        $this->authorizeMitra($tenagaOrder);
        $request->validate(['alasan' => 'required|min:10']);
        try {
            $this->tenagaService->tolakOrder($tenagaOrder, auth('mitra')->user()->id_mitra, $request->alasan);
            return redirect()->route('mitra.tenaga.index')->with('info', 'Order ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function mulaiKerja(TenagaOrder $tenagaOrder)
    {
        $this->authorizeMitra($tenagaOrder);
        try {
            $this->tenagaService->mulaiKerja($tenagaOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Mulai bekerja.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function selesaiKerja(TenagaOrder $tenagaOrder)
    {
        $this->authorizeMitra($tenagaOrder);
        try {
            $this->tenagaService->selesaiKerja($tenagaOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Pekerjaan selesai. Menunggu konfirmasi pelanggan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizeMitra(TenagaOrder $order): void
    {
        if ((int) $order->mitra_id !== auth('mitra')->user()->id_mitra) abort(403);
    }
}
