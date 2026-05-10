<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\{JastipOrder, JastipOrderItem, JastipStop};
use App\Services\JastipService;
use Illuminate\Http\Request;

class JastipController extends Controller
{
    public function __construct(private JastipService $jastipService) {}

    public function index()
    {
        $mitraId = auth('mitra')->user()->id_mitra;
        $orders  = JastipOrder::where('mitra_id', $mitraId)->latest()->paginate(15);
        return view('mitra.jastip.index', compact('orders'));
    }

    public function show(JastipOrder $jastipOrder)
    {
        $this->authorizeMitra($jastipOrder);
        $jastipOrder->load(['pelanggan', 'stops.items']);
        return view('mitra.jastip.show', ['order' => $jastipOrder]);
    }

    public function terima(JastipOrder $jastipOrder)
    {
        $this->authorizeMitra($jastipOrder);
        try {
            $this->jastipService->terimaOrder($jastipOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Order diterima! Mulailah perjalanan ke pickup.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function tolak(Request $request, JastipOrder $jastipOrder)
    {
        $this->authorizeMitra($jastipOrder);
        $request->validate(['alasan' => 'required|string|min:10']);

        try {
            $this->jastipService->tolakOrder($jastipOrder, auth('mitra')->user()->id_mitra, $request->alasan);
            return redirect()->route('mitra.jastip.index')->with('info', 'Order ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function tibaStop(JastipStop $stop)
    {
        $this->authorizeMitra($stop->order);
        try {
            $this->jastipService->tibaDiStop($stop, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Tiba di ' . $stop->nama_lokasi);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function checklistItem(Request $request, JastipOrderItem $item)
    {
        $this->authorizeMitra($item->order);
        $request->validate(['harga_asli' => 'required|numeric|min:0']);

        try {
            $this->jastipService->checklistItem($item, auth('mitra')->user()->id_mitra, $request->harga_asli);
            return back()->with('success', 'Item ' . $item->nama_barang . ' tercheck.');
        } catch (\RuntimeException $e) {
            return back()->with('warning', $e->getMessage());
        }
    }

    public function mulaiAntar(JastipOrder $jastipOrder)
    {
        $this->authorizeMitra($jastipOrder);
        try {
            $this->jastipService->mulaiAntar($jastipOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Mulai pengantaran ke pelanggan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function diantar(JastipOrder $jastipOrder)
    {
        $this->authorizeMitra($jastipOrder);
        try {
            $this->jastipService->diantar($jastipOrder, auth('mitra')->user()->id_mitra);
            return back()->with('success', 'Barang diantar. Menunggu konfirmasi pelanggan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizeMitra(JastipOrder $order): void
    {
        if ((int) $order->mitra_id !== auth('mitra')->user()->id_mitra) abort(403);
    }
}
