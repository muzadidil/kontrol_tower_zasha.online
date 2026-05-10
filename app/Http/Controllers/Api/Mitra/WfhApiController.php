<?php

namespace App\Http\Controllers\Api\Mitra;

use App\Http\Controllers\Controller;
use App\Models\WfhOrder;
use App\Services\WfhService;
use Illuminate\Http\Request;

class WfhApiController extends Controller
{
    public function __construct(private WfhService $wfhService) {}

    public function index(Request $request)
    {
        $orders = WfhOrder::where('mitra_id', $request->user()->id_mitra)
            ->with('pelanggan', 'mitraLayanan.masterLayanan')
            ->latest()->paginate(20);
        return response()->json($orders);
    }

    public function show(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->mitra_id !== $request->user()->id_mitra) abort(403);
        $wfhOrder->load('pelanggan', 'mitraLayanan.masterLayanan');
        return response()->json($wfhOrder);
    }

    public function terima(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->mitra_id !== $request->user()->id_mitra) abort(403);
        try {
            $this->wfhService->terimaOrder($wfhOrder, $request->user()->id_mitra);
            return response()->json(['message' => 'Order diterima.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function tolak(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->mitra_id !== $request->user()->id_mitra) abort(403);
        $request->validate(['alasan' => 'required|min:10']);
        try {
            $this->wfhService->tolakOrder($wfhOrder, $request->user()->id_mitra, $request->alasan);
            return response()->json(['message' => 'Order ditolak.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function kirimFile(Request $request, WfhOrder $wfhOrder)
    {
        if ((int) $wfhOrder->mitra_id !== $request->user()->id_mitra) abort(403);
        $request->validate(['file_url' => 'required|url']);
        try {
            $this->wfhService->kirimFile($wfhOrder, $request->user()->id_mitra, $request->file_url);
            return response()->json(['message' => 'File terkirim.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
