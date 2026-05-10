<?php

namespace App\Http\Controllers\Api\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\WfhOrder;
use App\Services\WfhService;
use Illuminate\Http\Request;

class WfhApiController extends Controller
{
    public function __construct(private WfhService $wfhService) {}

    public function index(Request $request)
    {
        $orders = WfhOrder::where('pelanggan_id', $request->user()->id_pelanggan)
            ->with('mitra', 'mitraLayanan.masterLayanan')
            ->latest()->paginate(20);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mitra_layanan_id'  => 'required|exists:mitra_layanans,id',
            'brief_description' => 'required|min:20',
            'reference_url'     => 'nullable|url',
            'quantity'          => 'nullable|numeric',
            'tipe_order'        => 'required|in:reguler,express',
        ]);

        try {
            $order = $this->wfhService->buatOrder($request->all(), $request->user()->id_pelanggan);
            return response()->json(['order' => $order], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->pelanggan_id !== $request->user()->id_pelanggan) abort(403);
        $wfhOrder->load('mitra', 'mitraLayanan.masterLayanan', 'escrowLedgers');
        return response()->json($wfhOrder);
    }

    public function konfirmasi(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->pelanggan_id !== $request->user()->id_pelanggan) abort(403);
        try {
            $this->wfhService->konfirmasiSelesai($wfhOrder, $request->user()->id_pelanggan);
            return response()->json(['message' => 'Order selesai.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function dispute(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->pelanggan_id !== $request->user()->id_pelanggan) abort(403);
        $request->validate(['deskripsi' => 'required|min:20']);
        try {
            $this->wfhService->bukaDispute($wfhOrder, $request->user()->id_pelanggan, $request->deskripsi);
            return response()->json(['message' => 'Dispute terbuka.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
