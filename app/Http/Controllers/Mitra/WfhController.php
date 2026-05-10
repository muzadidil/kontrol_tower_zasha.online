<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\WfhOrder;
use App\Services\WfhService;
use Illuminate\Http\Request;

class WfhController extends Controller
{
    public function __construct(private WfhService $wfhService) {}

    // Dashboard order masuk / aktif
    public function index()
    {
        $mitraId = auth('mitra')->user()->id_mitra;

        $orders = WfhOrder::where('mitra_id', $mitraId)
            ->latest()->paginate(15);

        return view('mitra.wfh.index', compact('orders'));
    }

    // Detail order
    public function show(WfhOrder $wfhOrder)
    {
        $this->authorizeMitra($wfhOrder);
        $wfhOrder->load(['pelanggan', 'mitraLayanan.masterLayanan', 'escrowLedgers']);
        return view('mitra.wfh.show', ['order' => $wfhOrder]);
    }

    // Terima order
    public function terima(WfhOrder $wfhOrder)
    {
        $this->authorizeMitra($wfhOrder);

        try {
            $this->wfhService->terimaOrder($wfhOrder, auth('mitra')->user()->id_mitra);
            return redirect()->route('mitra.wfh.show', $wfhOrder->id)
                ->with('success', 'Order diterima! Silakan mulai mengerjakan.');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Tolak order
    public function tolak(Request $request, WfhOrder $wfhOrder)
    {
        $this->authorizeMitra($wfhOrder);
        $request->validate(['alasan' => 'required|string|min:10']);

        try {
            $this->wfhService->tolakOrder($wfhOrder, auth('mitra')->user()->id_mitra, $request->alasan);
            return redirect()->route('mitra.wfh.index')
                ->with('info', 'Order ditolak dan saldo pelanggan dikembalikan.');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Kirim file hasil kerja
    public function kirimFile(Request $request, WfhOrder $wfhOrder)
    {
        $this->authorizeMitra($wfhOrder);
        $request->validate([
            'file_url' => 'required|url',
        ]);

        try {
            $this->wfhService->kirimFile($wfhOrder, auth('mitra')->user()->id_mitra, $request->file_url);
            return redirect()->route('mitra.wfh.show', $wfhOrder->id)
                ->with('success', 'File berhasil dikirim! Menunggu konfirmasi pelanggan.');
        } catch (\LogicException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function authorizeMitra(WfhOrder $order): void
    {
        if ((int) $order->mitra_id !== auth('mitra')->user()->id_mitra) {
            abort(403);
        }
    }
}
